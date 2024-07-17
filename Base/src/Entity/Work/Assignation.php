<?php

namespace App\Entity\Work;

use App\Entity\AbstractEntity;
use \Datetime;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Client\Project;
use App\Entity\User\User;

#[ORM\Entity()]
class Assignation extends AbstractEntity
{

    #[ORM\Column(type: "datetime")]
    private DateTime $startAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $deadline = null;

    #[ORM\Column(type: "float")]
    private float $duration = 1.0;

    #[ORM\ManyToOne(inversedBy: 'assignations')]
    private User $user;

    #[ORM\ManyToOne(inversedBy: 'assignations')]
    private ?Project $project;

    const HALF_DAY_ENTRY = [
        'AM' => 'AM',
        'PM' => 'PM'
    ];

    #[ORM\Column(type: "string")]
    private string $halfDay = "AM";

    public function __construct()
    {
        $this->startAt = new DateTime();
    }

    public function getStartAt()
    {
        return $this->startAt;
    }

    public function setStartAt(DateTime $date)
    {
        $this->startAt = $date;

        return $this;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function setDuration(int $duration)
    {
        $this->duration = $duration;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project ?: null;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;

        return $this;
    }

    public function getHalfDay(): string
    {
        return $this->halfDay;
    }

    public function setHalfDay(string $entry)
    {
        $this->halfDay = $entry;

        return $this;
    }

    public function getDeadline(): ?DateTime
    {
        return $this->deadline;
    }

    public function setDeadline(?DateTime $date): self
    {
        $this->deadline = $date;
        
        return $this;
    }
}