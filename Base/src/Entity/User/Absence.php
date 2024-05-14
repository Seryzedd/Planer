<?php

namespace App\Entity\User;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use \DateTime;
use App\Entity\User\User;
use App\Repository\AbsenceRepository;

#[ORM\Entity(repositoryClass: AbsenceRepository::class)]
class Absence extends AbstractEntity
{

    #[ORM\Column(type: "datetime")]
    private DateTime $fromDate;

    #[ORM\Column(type: "datetime")]
    private DateTime $toDate;

    const ABSENCE_TYPE_LIST = [
        'RTT' => 'RTT',
        'UNPAYED' => 'UNPAYED',
        'DISEASE' => 'DISEASE',
        'FAIR' => 'FAIR'
    ];

    #[ORM\Column(type: "string")]
    private string $type;

    /**
     * @var User
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'absences', cascade: ['persist', 'remove'])]
    private User $user;

    public function getFrom(): DateTime
    {
        return $this->fromDate;
    }

    public function setFrom(DateTime $date)
    {
        $this->fromDate = $date;
    }

    public function getTo(): DateTime
    {
        return $this->toDate;
    }

    public function setTo(DateTime $date)
    {
        $this->toDate = $date;
    }

    public function isOff(string $dateString)
    {
        $date = DateTime::createFromFormat('d/m/Y', $dateString);

        return $date->format('Y/d/m') >= $this->fromDate->format('Y/d/m') && $date->format('Y/d/m') <= $this->toDate->format('Y/d/m');
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }
}