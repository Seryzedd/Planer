<?php

namespace App\Entity\Translations;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Work\Assignation;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;
use App\Entity\User\Team;
use App\Interface\InvoiceSubjectInterface;

#[ORM\Entity()]
class TeamTranslation extends AbstractEntity
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
     * @var Team|null
     */
    #[ORM\ManyToOne(inversedBy: 'translations')]
    private ?Team $team;

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
        return $this->team->getOriginalName();
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

    public function getOriginalDescription(): string
    {
        return $this->team->getOriginalDescription();
    }

    public function setTeam(Team $team): self
    {
        $this->team = $team;
        return $this;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }
}