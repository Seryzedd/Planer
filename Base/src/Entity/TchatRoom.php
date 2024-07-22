<?php

namespace App\Entity;

use App\Entity\User\User;
use App\Repository\TchatRoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TchatRoomRepository::class)]
class TchatRoom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tchatRooms')]
    private Collection $title;

    public function __construct()
    {
        $this->title = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getTitle(): Collection
    {
        return $this->title;
    }

    public function addTitle(User $title): static
    {
        if (!$this->title->contains($title)) {
            $this->title->add($title);
        }

        return $this;
    }

    public function removeTitle(User $title): static
    {
        $this->title->removeElement($title);

        return $this;
    }
}
