<?php

namespace App\Entity\Client;

use App\Entity\AbstractEntity;
use App\Entity\HoursSold;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Work\Assignation;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;
use App\Entity\Translations\ProjectTranslation;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\Event\PostLoadEventArgs;
use App\Entity\Listener\ProjectListener;

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

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: ProjectTranslation::class)]
    private Collection $translations;

    private ?ProjectTranslation $currentTranslation = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: HoursSold::class, cascade: ['persist'])]
    private Collection $hoursSold;

    public function __construct()
    {
        $this->assignations = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->hoursSold = new ArrayCollection();
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {

        if ($this->currentTranslation) {
            if ($this->currentTranslation->getName()) {
                return $this->currentTranslation->getName();
            }
        }

        return $this->name;
    }

    public function getOriginalName()
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
        if ($this->currentTranslation) {
            if ($this->currentTranslation->getDescription()) {
                return $this->currentTranslation->getDescription();
            }
        }

        return $this->description;
    }

    public function getOriginalDescription()
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

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(ProjectTranslation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setProject($this);
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

    public function setCurrentTranslation(ProjectTranslation $translation)
    {
        $this->currentTranslation = $translation;
    }

    public function getHoursSold(): Collection
    {
        return $this->hoursSold;
    }

    public function addHoursSold(HoursSold $hours): static
    {
        if (!$this->hoursSold->contains($hours)) {
            $this->hoursSold->add($hours);
            $hours->setProject($this);
        }

        return $this;
    }

    public function removeProject(HoursSold $hours): static
    {
        if ($this->hoursSold->removeElement($hours)) {
            // set the owning side to null (unless already changed)
            if ($hours->getProject() === $this) {
                $hours->getProject(null);
            }
        }

        return $this;
    }
}