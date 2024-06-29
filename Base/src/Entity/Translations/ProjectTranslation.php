<?php

namespace App\Entity\Translations;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Work\Assignation;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;
use App\Entity\Client\Project;
use App\Interface\InvoiceSubjectInterface;

#[ORM\Entity()]
class ProjectTranslation extends AbstractEntity
{
    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $language = '';

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $description = '';

    /**
     * @var Project|null
     */
    #[ORM\ManyToOne(inversedBy: 'translations')]
    private ?Project $project;

    public function __construct(string $language = '')
    {
        $this->language = $language;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOriginalName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;
        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}