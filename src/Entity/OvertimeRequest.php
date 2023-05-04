<?php

namespace App\Entity;

use App\Repository\OvertimeRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OvertimeRequestRepository::class)]
#[ORM\Table(name: 'overtime')]
class OvertimeRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'overtimeRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $employee = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cause = null;

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

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): self
    {
        $this->employee = $employee;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getCause(): ?string
    {
        return $this->cause;
    }

    public function setCause(?string $cause): self
    {
        $this->cause = $cause;

        return $this;
    }
}
