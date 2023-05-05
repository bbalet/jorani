<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
#[ORM\Table(name: 'contracts')]
class Contract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $weeklyDuration = null;

    #[ORM\Column(nullable: true)]
    private ?int $dailyDuration = null;

    #[ORM\ManyToMany(targetEntity: LeaveType::class, inversedBy: 'contracts')]
    private Collection $leaveTypes;

    public function __construct()
    {
        $this->leaveTypes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

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

    public function getWeeklyDuration(): ?int
    {
        return $this->weeklyDuration;
    }

    public function setWeeklyDuration(?int $weeklyDuration): self
    {
        $this->weeklyDuration = $weeklyDuration;

        return $this;
    }

    public function getDailyDuration(): ?int
    {
        return $this->dailyDuration;
    }

    public function setDailyDuration(?int $dailyDuration): self
    {
        $this->dailyDuration = $dailyDuration;

        return $this;
    }

    /**
     * @return Collection<int, LeaveType>
     */
    public function getLeaveTypes(): Collection
    {
        return $this->leaveTypes;
    }

    public function addLeaveType(LeaveType $leaveType): self
    {
        if (!$this->leaveTypes->contains($leaveType)) {
            $this->leaveTypes->add($leaveType);
        }

        return $this;
    }

    public function removeLeaveType(LeaveType $leaveType): self
    {
        $this->leaveTypes->removeElement($leaveType);

        return $this;
    }
}
