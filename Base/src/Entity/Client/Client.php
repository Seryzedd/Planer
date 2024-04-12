<?php

namespace App\Entity\Client;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity()]
class Client extends AbstractEntity
{
    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    private string $name = "";

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Project::class, cascade: ['persist'])]
    private Collection $projects;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $companyId = null;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->addProject(new Project());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function addProject(Project $project)
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setClient($this);
        }

        return $this;
    }

    public function getProjects()
    {
        return $this->projects;
    }

    public function setCompanyId(int $id)
    {
        $this->companyId = $id;

        return $this;
    }
}