<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
#[ORM\Table(name: 'contracts')]
class Contract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $name = null;

    #[ORM\Column(name: 'startentdate', length: 5)]
    private ?string $startEntDate = null;

    #[ORM\Column(name: 'endentdate', length: 5)]
    private ?string $endEntDate = null;

    #[ORM\Column(name: 'weekly_duration', nullable: true)]
    private ?int $weeklyDuration = null;

    #[ORM\Column(name: 'daily_duration', nullable: true)]
    private ?int $dailyDuration = null;

    #[ORM\Column(name: 'default_leave_type', nullable: true)]
    private ?int $defaultLeaveType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartentdate(): ?string
    {
        return $this->startentdate;
    }

    public function setStartentdate(string $startentdate): self
    {
        $this->startentdate = $startentdate;

        return $this;
    }

    public function getEndentdate(): ?string
    {
        return $this->endentdate;
    }

    public function setEndentdate(string $endentdate): self
    {
        $this->endentdate = $endentdate;

        return $this;
    }

    public function getWeeklyDuration(): ?int
    {
        return $this->weekly_duration;
    }

    public function setWeeklyDuration(?int $weekly_duration): self
    {
        $this->weekly_duration = $weekly_duration;

        return $this;
    }

    public function getDailyDuration(): ?int
    {
        return $this->daily_duration;
    }

    public function setDailyDuration(?int $daily_duration): self
    {
        $this->daily_duration = $daily_duration;

        return $this;
    }

    public function getDefaultLeaveType(): ?int
    {
        return $this->default_leave_type;
    }

    public function setDefaultLeaveType(?int $default_leave_type): self
    {
        $this->default_leave_type = $default_leave_type;

        return $this;
    }
}
