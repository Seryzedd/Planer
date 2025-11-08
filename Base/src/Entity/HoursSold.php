<?php

namespace App\Entity;

use App\Entity\Client\Project;
use App\Repository\HoursSoldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HoursSoldRepository::class)]
class HoursSold
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?float $delay = null;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'hoursSold', cascade: ['persist', 'remove'])]
    private Project $project;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDelay(): ?float
    {
        return $this->delay;
    }

    public function setDelay(float $delay): static
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;
    }
}
