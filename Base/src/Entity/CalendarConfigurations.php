<?php

namespace App\Entity;

use App\Repository\CalendarConfigurationsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalendarConfigurationsRepository::class)]
class CalendarConfigurations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $useCounter = true;

    #[ORM\OneToOne(inversedBy: 'calendarConfigurations', cascade: ['persist', 'remove'])]
    private ?Company $Company = null;

    #[ORM\Column]
    private bool $activeTchat = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isUseCounter(): ?bool
    {
        return $this->useCounter;
    }

    public function setUseCounter(bool $useCounter): static
    {
        $this->useCounter = $useCounter;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->Company;
    }

    public function setCompany(?Company $Company): static
    {
        $this->Company = $Company;

        return $this;
    }

    public function setActiveTchat(bool $active)
    {
        $this->activeTchat = $active;

        return $this;
    }

    public function isActiveTchat()
    {
        return $this->activeTchat ?: false;
    }
}
