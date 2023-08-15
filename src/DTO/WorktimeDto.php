<?php

namespace App\DTO;

class WorktimeDto
{
    private string $title;
    private string $description;
    private string $timeAmount;
    private string $date;
    private int $projectId;

    public function __construct(string $title, string $description, string $timeAmount, string $date, int $projectId)
    {
        $this->title = trim($title);
        $this->description = trim($description);
        $this->timeAmount = trim($timeAmount);
        $this->date = $date;
        $this->projectId = $projectId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTimeAmount(): string
    {
        return $this->timeAmount;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }
}
