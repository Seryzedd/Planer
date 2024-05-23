<?php

namespace App\Entity\Client;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Work\Assignation;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;

#[ORM\Entity()]
class Project extends AbstractEntity
{
    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = '';

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $deadline = null;

    /**
     * @var Client|null
     */
    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Client $client = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Assignation::class)]
    private Collection $assignations;

    public function __construct()
    {
        $this->assignations = new ArrayCollection();
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    public function getAssignations()
    {
        return $this->assignations ?: null;
    }

    public function addAssignation(Assignation $assignation)
    {
        if (!$this->assignations->contains($assignation)) {
            $this->assignations->add($assignation);
            $assignation->setProject($this);
        }

        return $this;
    }

    public function getDeadline(): ?DateTime
    {
        return $this->deadline;
    }

    public function setDeadline(?DateTime $date)
    {
        $this->deadline = $date;
    }
}