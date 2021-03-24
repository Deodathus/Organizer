<?php

namespace App\Entity;

use App\Repository\WorkTimeEntryEntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WorkTimeEntryEntityRepository::class)
 * @ORM\Table(name="work_time_entry_entity")
 */
class WorktimeEntryEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $time_amount;

    /**
     * @ORM\Column(type="float")
     */
    private float $time_amount_in_minutes;

    /**
     * @ORM\Column(type="date")
     */
    private \DateTime $date;

    /**
     * WorktimeEntryEntity constructor.
     *
     * @param string $title
     * @param string $description
     * @param string $time_amount
     * @param string $date
     */
    public function __construct(string $title, string $description, string $time_amount, string $date)
    {
        $this->title = $title;
        $this->description = $description;
        $this->time_amount = $time_amount;
        $this->date = new \DateTime($date);

        $this->updateTimeAmountInMinutes();
    }

    public function update(string $title, string $description, string $timeAmount, string $date): self
    {
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setTimeAmount($timeAmount);
        $this->setDate(new \DateTime($date));

        $this->updateTimeAmountInMinutes();

        return $this;
    }

    private function updateTimeAmountInMinutes(): void
    {
        preg_match("/(\d+)[h]/",  $this->time_amount, $hourMatch);
        preg_match("/(\d+)[m]/",  $this->time_amount, $minuteMatch);

        $timeAmountInMinutes = 0;
        if (array_key_exists(1, $hourMatch)) {
            $timeAmountInMinutes += (int) $hourMatch[1] * 60;
        }

        if (array_key_exists(1, $minuteMatch)) {
            $timeAmountInMinutes += $minuteMatch[1];
        }
        $this->time_amount_in_minutes = $timeAmountInMinutes;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
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
        return $this->time_amount;
    }

    public function setTimeAmount(string $time_amount): self
    {
        $this->time_amount = $time_amount;

        return $this;
    }

    public function getTimeAmountInMinutes(): ?float
    {
        return $this->time_amount_in_minutes;
    }

    public function setTimeAmountInMinutes(float $time_amount_in_minutes): self
    {
        $this->time_amount_in_minutes = $time_amount_in_minutes;

        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getStringDate(): string
    {
        return $this->date->format('d-m-Y');
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }
}
