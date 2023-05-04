<?php

namespace App\Entity;

use App\Repository\LeaveRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeaveRequestRepository::class)]
#[ORM\Table(name: 'leaves')]
class LeaveRequest
{
    /**
     * Leave requests can start and end either in morning or afternoon
     */
    const MORNING = 1;
    const AFTERNOON = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    private ?string $cause = null;

    #[ORM\Column]
    private ?int $startDateType = null;

    #[ORM\Column]
    private ?int $endDateType = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\ManyToOne(inversedBy: 'leaveRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LeaveType $type = null;

    #[ORM\Column(type: Types::JSON)]
    private array $status = [];

    public function getStatus(): array
    {
        return $this->status;
    }

    public function setStatus(array $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCause(): ?string
    {
        return $this->cause;
    }

    public function setCause(string $cause): self
    {
        $this->cause = $cause;

        return $this;
    }

    public function getStartDateType(): ?int
    {
        return $this->startDateType;
    }

    public function setStartDateType(int $startDateType): self
    {
        if (!in_array($startDateType, array(self::MORNING, self::AFTERNOON))) {
            throw new \InvalidArgumentException("Must be either Morning or Afternoon");
        }
        $this->startDateType = $startDateType;
        return $this;
    }

    public function getEndDateType(): ?int
    {
        return $this->endDateType;
    }

    public function setEndDateType(int $endDateType): self
    {
        if (!in_array($endDateType, array(self::MORNING, self::AFTERNOON))) {
            throw new \InvalidArgumentException("Must be either Morning or Afternoon");
        }
        $this->endDateType = $endDateType;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getType(): ?LeaveType
    {
        return $this->type;
    }

    public function setType(?LeaveType $type): self
    {
        $this->type = $type;

        return $this;
    }
}
