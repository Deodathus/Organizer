<?php

namespace App\DTO;

class WorktimeDto
{
    private string $title;
    private string $description;
    private string $timeAmount;
    private string $date;

    /**
     * WorktimeDto constructor.
     *
     * @param string $title
     * @param string $description
     * @param string $timeAmount
     * @param string $date
     */
    public function __construct(string $title, string $description, string $timeAmount, string $date)
    {
        $this->title = $title;
        $this->description = $description;
        $this->timeAmount = $timeAmount;
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getTimeAmount(): string
    {
        return $this->timeAmount;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }
}
