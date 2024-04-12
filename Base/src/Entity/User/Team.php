<?php

namespace App\Entity\User;

use App\Entity\User\User;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\AbstractEntity;

/**
 * Team entity class
 */
#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team extends AbstractEntity
{

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist'])]
    private ?User $lead = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private string $companyId = '';

    /**
     * @return void
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setTeam($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            $user->setTeam(null);
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLead()
    {
        return $this->lead;
    }

    public function setLead(User $user)
    {
        $this->lead = $user;

        return $this;
    }

    public function getCompanyId()
    {
        return $this->companyId;
    }

    public function setCompanyId(string $id)
    {
        $this->companyId = $id;
        return $this;
    }
}
