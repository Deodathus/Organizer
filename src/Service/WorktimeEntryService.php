<?php

namespace App\Service;

use App\DTO\WorktimeDto;
use App\Entity\WorktimeEntryEntity;
use App\Repository\WorktimeEntryEntityRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class WorktimeEntryService implements WorktimeEntryServiceInterface
{
    private WorktimeEntryEntityRepository $worktimeEntryRepository;

    public function __construct(WorktimeEntryEntityRepository $worktimeEntryRepository)
    {
        $this->worktimeEntryRepository = $worktimeEntryRepository;
    }

    public function fetch(int $id): WorktimeEntryEntity
    {
        return $this->worktimeEntryRepository->fetch($id);
    }

    public function fetchAll(): array
    {
        return $this->worktimeEntryRepository->fetchAll();
    }

    public function fetchLast(int $lastDaysAmount): array
    {
        $startDate = Carbon::now()->subDays($lastDaysAmount);
        $endDate = Carbon::now();

        $carbon = CarbonPeriod::dates($startDate, $endDate);

        $dates = [];
        foreach ($carbon as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $lastDaysEntries = $this->worktimeEntryRepository->fetchByDateIn($dates);

        return $this->sort($lastDaysEntries);
    }

    private function sort(array $lastDaysEntries): array
    {
        $sortedEntitiesArray = [];

        foreach ($lastDaysEntries as $lastDaysEntry) {
            $sortedEntitiesArray[$lastDaysEntry->getStringDate()]['entries'][] = $lastDaysEntry;
            $sortedEntitiesArray[$lastDaysEntry->getStringDate()]['totalAmountInMinutes'] = 0;
        }

        /**
         * @var string $key
         * @var WorktimeEntryEntity $value
         */
        foreach ($sortedEntitiesArray as $key => $sortedEntityArray) {
            foreach ($sortedEntityArray as $sortedEntity) {
                if (is_array($sortedEntity)) {
                    foreach ($sortedEntity as $entity) {
                        if (is_a($entity, WorktimeEntryEntity::class)) {
                            $sortedEntitiesArray[$key]['totalAmountInMinutes'] += $entity->getTimeAmountInMinutes();
                        }
                    }
                }
            }
        }

        foreach ($sortedEntitiesArray as $key => $sortedDate) {
            $difference = 450 - $sortedDate['totalAmountInMinutes'];
            if ($difference <= 0) {
                $sortedEntitiesArray[$key]['doneInPercentage'] = 100;
            } else {
                $sortedEntitiesArray[$key]['doneInPercentage'] = ($sortedDate['totalAmountInMinutes'] / 450) * 100;
                $sortedEntitiesArray[$key]['doneInPercentage'] = round($sortedEntitiesArray[$key]['doneInPercentage'], 2);
            }
        }

        return $sortedEntitiesArray;
    }

    public function store(WorktimeDto $worktimeDto): void
    {
        $worktimeEntryEntity = new WorktimeEntryEntity(
            $worktimeDto->getTitle(),
            $worktimeDto->getDescription(),
            $worktimeDto->getTimeAmount(),
            $worktimeDto->getDate()
        );

        $this->worktimeEntryRepository->store($worktimeEntryEntity);
    }

    public function update(int $id, WorktimeDto $worktimeDto): void
    {
        $this->worktimeEntryRepository->fetch($id)->update(
            $worktimeDto->getTitle(),
            $worktimeDto->getDescription(),
            $worktimeDto->getTimeAmount(),
            $worktimeDto->getDate()
        );

        $this->worktimeEntryRepository->update();
    }

    public function remove(int $id): void
    {
        $this->worktimeEntryRepository->remove($this->worktimeEntryRepository->fetch($id));
    }
}
