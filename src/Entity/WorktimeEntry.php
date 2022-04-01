<?php

namespace App\Entity;

use App\Repository\WorktimeEntryRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WorktimeEntryRepository::class)
 * @ORM\Table(name="worktime_entries")
 */
class WorktimeEntry
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $timeAmount;

    /**
     * @ORM\Column(type="float")
     */
    private float $timeAmountInMinutes;

    /**
     * @ORM\Column(type="date")
     */
    private DateTime $date;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="worktimeEntries")
     * @ORM\JoinColumn(nullable=false)
     */
    private Project $project;

    public function __construct(string $title, string $description, string $timeAmount, string $date, Project $project)
    {
        $this->title = $title;
        $this->description = $description;
        $this->timeAmount = $timeAmount;
        $this->date = new DateTime($date);
        $this->project = $project;

        $this->updateTimeAmountInMinutes();
    }

    public function update(string $title, string $description, string $timeAmount, string $date): self
    {
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setTimeAmount($timeAmount);
        $this->setDate(new DateTime($date));

        $this->updateTimeAmountInMinutes();

        return $this;
    }

    private function updateTimeAmountInMinutes(): void
    {
        preg_match("/(\d+)[h]/",  $this->timeAmount, $hourMatch);
        preg_match("/(\d+)[m]/",  $this->timeAmount, $minuteMatch);

        $timeAmountInMinutes = 0;
        if (array_key_exists(1, $hourMatch)) {
            $timeAmountInMinutes += (int) $hourMatch[1] * 60;
        }

        if (array_key_exists(1, $minuteMatch)) {
            $timeAmountInMinutes += $minuteMatch[1];
        }
        $this->timeAmountInMinutes = $timeAmountInMinutes;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTimeAmount(): ?string
    {
        return $this->timeAmount;
    }

    public function setTimeAmount(string $timeAmount): self
    {
        $this->timeAmount = $timeAmount;

        return $this;
    }

    public function getTimeAmountInMinutes(): ?float
    {
        return $this->timeAmountInMinutes;
    }

    public function setTimeAmountInMinutes(float $timeAmountInMinutes): self
    {
        $this->timeAmountInMinutes = $timeAmountInMinutes;

        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getStringDate(): string
    {
        return $this->date->format('d-m-Y');
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
