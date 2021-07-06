<?php

namespace App\DTO;

class WorktimeDto
{
    private string $title;
    private string $description;
    private string $timeAmount;
    private string $date;
    private int $projectId;

    /**
     * WorktimeDto constructor.
     *
     * @param string $title
     * @param string $description
     * @param string $timeAmount
     * @param string $date
     * @param int $projectId
     */
    public function __construct(string $title, string $description, string $timeAmount, string $date, int $projectId)
    {
        $this->title = trim($title);
        $this->description = trim($description);
        $this->timeAmount = trim($timeAmount);
        $this->date = $date;
        $this->projectId = $projectId;
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

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->projectId;
    }
}
