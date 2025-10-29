<?php

namespace App\Entity;

use App\Entity\User\User;
use App\Entity\TchatRoom;
use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'TchatMessage')]
    private User $author;

    #[ORM\ManyToOne(inversedBy: 'messages', targetEntity: TchatRoom::class)]
    private TchatRoom $room;

    #[ORM\Column(type: Types::TEXT)]
    private string $content = "";

    /**
     * @var array|null
     */
    #[ORM\Column(type: 'json')]
    private ?array $readedUsersId = [];

    public function __construct()
    {
        $this->createdAt = new \Datetime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $user)
    {
        $this->author = $user;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function setRoom(TchatRoom $room)
    {
        $this->room = $room;

        return $this;
    }

    public function getRoom()
    {
        return $this->room;
    }

    public function getReadedUsersId(): array
    {
        return $this->readedUsersId ?: [];
    }

    /**
     * @param User $user
     * @return $this
     */
    public function addReadedUsersId(User $user): static
    {
        $this->readedUsersId[] = $user->getId();

        return $this;
    }

    public function setReadedUsersId(array $usersIds)
    {
        $this->readedUsersId = $usersIds;
        return $this;
    }

    public function isUserReaded(User $user): bool
    {
        return in_array($user->getId(), $this->getReadedUsersId());
    }

    public function toArray()
    {

        return [
            'id' => $this->id,
            'createdAt' => ['day' => $this->createdAt->format('d/m/Y'), 'hour' => $this->createdAt->format('G:i')],
            'author' => $this->author->toArray(),
            'content' => $this->content,
            'readedUsersIds' => $this->getReadedUsersId()
        ];
    }
}
