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

    public function setFrom(DateTime $date, string $moment = 'AM'):void
    {
        if($moment === 'AM') {
            $hour = '00:00';
        } else {
            $hour = '02:00';
        }

        $this->fromDate = DateTime::createFromFormat('d/m/Y h:i A', $date->format('d/m/Y') . " ". $hour ." " . $moment);
    }

    public function getTo(): DateTime
    {
        return $this->toDate;
    }

    public function setTo(DateTime $date, string $moment = 'AM'): void
    {
        if($moment === 'AM') {
            $hour = '00:00';
        } else {
            $hour = '02:00';
        }

        $this->toDate = DateTime::createFromFormat('d/m/Y h:i A', $date->format('d/m/Y') . " ". $hour ." " . $moment);
    }

    public function isOff(string $dateString, string $momentKey = 'AM'): bool
    {
        $date = DateTime::createFromFormat('d/m/Y h:i A', $dateString . " " . ($momentKey === 'AM'? '00:00' : '02:00') . " " . $momentKey);
        
        return $date >= $this->getFrom() && $date <= $this->getTo();
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