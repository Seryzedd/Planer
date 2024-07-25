<?php

namespace App\Entity;

use App\Entity\User\User;
use App\Repository\TchatRoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Message;

#[ORM\Entity(repositoryClass: TchatRoomRepository::class)]
class TchatRoom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name = "";

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tchatRooms')]
    private Collection $title;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'room', cascade: ['persist', 'remove'])]
    private Collection $messages;

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
            $title->addTchatRoom($this);
        }

        return $this;
    }

    public function removeTitle(User $title): static
    {
        $this->title->removeElement($title);

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

    public function getMessages()
    {
        return $this->messages;
    }

    public function setMessages(Collection $messages)
    {
        $this->messages = $messages;

        return $this;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setRoom($this);
        }

        return $this;
    }

    public function removeMessage(Message $message)
    {
        $this->messages->removeElement($message);

        return $this;
    }

    public function toArray()
    {
        $users = [];
        $messages = [];

        foreach ($this->title as $user) {
            $users[] = $user->toArray();
        }

        foreach ($this->messages as $message) {
            $messages[] = $message->toArray();
        }
        return [
                'id' => $this->id,
                'name' => $this->name,
                'user' => $users,
                'messages' => $messages
            ]
        ;
    }
}
