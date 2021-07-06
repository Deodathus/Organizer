<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class ProjectEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $title;

    /**
     * @ORM\OneToMany(targetEntity=WorktimeEntryEntity::class, mappedBy="projectEntity")
     */
    private Collection $worktimeEntries;

    public function __construct(string $title)
    {
        $this->worktimeEntries = new ArrayCollection();
        $this->title = $title;
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

    /**
     * @return Collection|WorktimeEntryEntity[]
     */
    public function getWorktimeEntries(): Collection
    {
        return $this->worktimeEntries;
    }

    public function addWorkimeEntry(WorktimeEntryEntity $worktimeEntry): self
    {
        if (!$this->worktimeEntries->contains($worktimeEntry)) {
            $this->worktimeEntries[] = $worktimeEntry;
            $worktimeEntry->setProjectEntity($this);
        }

        return $this;
    }

    public function removeWorktimeEntry(WorktimeEntryEntity $worktimeEntry): self
    {
        if ($this->worktimeEntries->removeElement($worktimeEntry)) {
            // set the owning side to null (unless already changed)
            if ($worktimeEntry->getProjectEntity() === $this) {
                $worktimeEntry->setProjectEntity(null);
            }
        }

        return $this;
    }
}
