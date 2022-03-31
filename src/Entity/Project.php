<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private string $title;

    /**
     * @ORM\OneToMany(targetEntity=WorktimeEntry::class, mappedBy="project")
     */
    private Collection $worktimeEntries;

    #[Pure] public function __construct(string $title)
    {
        $this->worktimeEntries = new ArrayCollection();

        $this->title = $title;
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

    /**
     * @return Collection<WorktimeEntry>
     */
    public function getWorktimeEntries(): Collection
    {
        return $this->worktimeEntries;
    }

    public function addWorktimeEntry(WorktimeEntry $worktimeEntry): self
    {
        if (!$this->worktimeEntries->contains($worktimeEntry)) {
            $this->worktimeEntries[] = $worktimeEntry;
            $worktimeEntry->setProject($this);
        }

        return $this;
    }

    public function removeWorktimeEntry(WorktimeEntry $worktimeEntry): self
    {
        if ($this->worktimeEntries->removeElement($worktimeEntry) && $worktimeEntry->getProject() === $this) {
            $worktimeEntry->setProject(null);
        }

        return $this;
    }
}
