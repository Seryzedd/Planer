<?php

namespace App\Entity\User;

use App\Entity\AbstractEntity;
use App\Entity\CalendarEvent;
use App\Entity\Company;
use App\Entity\Message;
use App\Entity\TchatRoom;
use App\Entity\User\Team;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Work\Assignation;
use App\Entity\User\Absence;
use App\Repository\UserRepository;
use App\Entity\User\Security\PasswordResetting;
use \DateTime;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploader;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['userName'], message: 'There is already an account with this userName')]
class User extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface
{

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $userName = '';

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $firstName = '';

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $lastName = '';

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private string $email = '';

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $job = '';

    const JOBS = [
        'DEV' => 'dev',
        'CONSULTANT' => 'consultant',
        'PROJECT_MANAGER' => 'project manager'
    ];
    
    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $password = '';

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string')]
    private string $headshot = '';

    /**
     * @var Collection
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Schedule::class, cascade: ['persist', 'remove'])]
    private Collection $schedule;

    /**
     * @var PasswordResetting|null
     */
    #[ORM\OneToOne(inversedBy: 'user', targetEntity: PasswordResetting::class, cascade: ['persist', 'remove'])]
    private ?PasswordResetting $PasswordResetting = null;

    /**
     * @var Collection
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity:Assignation::class)]
    private Collection $assignations;

    const ROLE_LIST = [
        'Admin' => 'ROLE_ADMIN',
        'Company leader' => 'ROLE_COMPANY_LEADER',
        'User' => 'ROLE_USER',
        'Team Manager' => 'ROLE_TEAM_MANAGER'
    ];

    /**
     * @var array
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var Team|null
     */
    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Team $team = null;

    /**
     * @var null|Company
     */
    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Company $company = null;

    /**
     * @var Collection
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity:Absence::class)]
    private Collection $absences;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\ManyToMany(targetEntity: TchatRoom::class, mappedBy: 'title')]
    private Collection $tchatRooms;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Message::class)]
    private Collection $TchatMessage;

    /**
     * @var Collection<int, CalendarEvent>
     */
    #[ORM\ManyToMany(targetEntity: CalendarEvent::class, mappedBy: 'user')]
    #[ORM\OrderBy(["StartAt"=>"DESC"])] 
    private Collection $calendarEvents;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->assignations = new ArrayCollection();
        $this->schedule = new ArrayCollection();
        $this->absences = new ArrayCollection();
        $this->schedule->add(new Schedule($this));
        $this->tchatRooms = new ArrayCollection();
        $this->TchatMessage = new ArrayCollection();
        $this->calendarEvents = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     * @return void
     */
    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return void
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return void
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getJob(): string
    {
        return $this->job;
    }

    /**
     * @param string $job
     * @return void
     */
    public function setJob(string $job): void
    {
        $this->job = $job;
    }

    /**
     * @return Collection
     */
    public function getSchedule(): Collection
    {
        return $this->schedule;
    }

    /**
     * @param Collection $schedule
     * @return void
     */
    public function setSchedule(Collection $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function addSchedule(Schedule $schedule): self
    {
        $this->schedule->add($schedule);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAssignations(): Collection
    {
        return $this->assignations;
    }

    /**
     * @param Collection $assignations
     * @return void
     */
    public function setAssignations(Collection $assignations): void
    {
        $this->assignations = $assignations;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function addRole(string $role): static
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function removeRole(string $role)
    {
        unset($this->roles[array_search($role, $this->roles)]);

        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->userName;
    }

    public function getLead(): ?Team
    {
        return $this->Lead;
    }

    public function setLead(?Team $Lead): static
    {
        $this->Lead = $Lead;

        return $this;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;

        $company->addUser($this);

        return $this;
    }

    public function setTeam(?Team $team)
    {
        $this->team = $team;

        if($team) {
            $team->addUser($this);
        }
        
        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function addAbsence(Absence $absence)
    {
        $absence->setUser($this);

        $this->absences->add($absence);
    }

    public function getAbsences()
    {
        return $this->absences;
    }

    public function isWorking(string $date, string $momentKey = 'AM')
    {
        
        foreach($this->absences as $key => $absence) {
            if ($absence->isOff($date, $momentKey)) {
                return $absence;
            }
        }

        $hour = '02:00';
        if ($momentKey == "AM") {
            $hour = '02:00';
        }

        try {
            $dateObject = DateTime::createFromFormat('d/m/Y h:i A', $date . " 02:00 " . $momentKey);
        } catch (\Throwable $th) {
            dump($th, $momentKey);
            die;
        }

        $schedule = $this->getScheduleByDate($dateObject);

        foreach($schedule->getDays() as $day) {
            if ($dateObject->format('l') === $day->getName()) {
                if ($momentKey === "AM") {
                    return $day->getMorning()->isWorking();
                } else {
                    return $day->getAfternoon()->isWorking();
                }
            }
        }

        return false;
    }

    public function getPasswordResetting()
    {
        return $this->PasswordResetting;
    }

    public function setPasswordResetting(PasswordResetting $passwordReset)
    {
        $this->PasswordResetting = $passwordReset;

        return $this;
    }

    public function getMostRecentSchedule()
    {
        return $this->getScheduleOrderByDates()->current();
    }

    public function getScheduleOrderByDates()
    {
        $iterator = $this->schedule->getIterator();
        $iterator->uasort(function (Schedule $a, Schedule $b) {
            
            return $b->getStartAt() <> $a->getStartAt();
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    public function getScheduleByDate(DateTime $date)
    {
        $scheduleByDate = $this->getScheduleOrderByDates();
        $current = null;
        
        foreach ($scheduleByDate as $key => $schedule) {
            if (!$current) {
                $current = $schedule;
            }
            
            if($schedule->getStartAt() > $date) {
                continue;
            } elseif ($schedule->isActive() && $schedule->getStartAt() < $date) {
                $current = $schedule;
            } else {
                $current = $schedule;
            }
        }

        return $current;
    }

    public function getHeadshot(): ?string
    {
        return $this->headshot;
    }

    public function setHeadshot(?string $url = null): self
    {
        $this->headshot = $url;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, TchatRoom>
     */
    public function getTchatRooms(): Collection
    {
        return $this->tchatRooms;
    }

    public function addTchatRoom(TchatRoom $tchatRoom): static
    {
        if (!$this->tchatRooms->contains($tchatRoom)) {
            $this->tchatRooms->add($tchatRoom);
            $tchatRoom->addTitle($this);
        }

        return $this;
    }

    public function removeTchatRoom(TchatRoom $tchatRoom): static
    {
        if ($this->tchatRooms->removeElement($tchatRoom)) {
            $tchatRoom->removeTitle($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, TchatRoom>
     */
    public function getTchatMessages(): Collection
    {
        return $this->TchatMessage;
    }

    public function setTchatMessages(?Collection $TchatMessage): static
    {
        $this->TchatMessage = $TchatMessage;

        return $this;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->TchatMessage->contains($message)) {
            $this->TchatMessage->add($message);
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->TchatMessage->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getTchatMessage() === $this) {
                $message->setTchatMessage(null);
            }
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'username' => $this->userName,
            'lastName' => $this->lastName,
            'headshot' => $this->headshot,
            'firstName' => $this->firstName,
            'email' => $this->email
        ];
    }

    /**
     * @return Collection<int, CalendarEvent>
     */
    public function getCalendarEvents(): Collection
    {
        return $this->calendarEvents;
    }

    public function addCalendarEvent(CalendarEvent $calendarEvent): static
    {
        if (!$this->calendarEvents->contains($calendarEvent)) {
            $this->calendarEvents->add($calendarEvent);
            $calendarEvent->addUser($this);
        }

        return $this;
    }

    public function removeCalendarEvent(CalendarEvent $calendarEvent): static
    {
        if ($this->calendarEvents->removeElement($calendarEvent)) {
            $calendarEvent->removeUser($this);
        }

        return $this;
    }

    public function getEventByDate(DateTime $date, string $hour, string $minutes)
    {
        $current = clone $date;

        $current->setTime($hour, $minutes);
        $events = $this->calendarEvents->filter(function(CalendarEvent $event) use ($current) {
            return $current >= $event->getStartAt() && $current <= $event->getEndAt();
        });

        return $events;
    }

    public function getEventsByWeekNumber(int $week, int $year): Collection
    {
        return $this->calendarEvents->filter(function(CalendarEvent $event) use ($week, $year) {
            return $year . $week >= $event->getStartAt()->format('YW') && $year . $week <= $event->getEndAt()->format('YW');
        });
    }
}