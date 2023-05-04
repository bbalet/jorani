<?php

namespace App\Entity;

use App\Repository\LeaveTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeaveTypeRepository::class)]
#[ORM\Table(name: 'types')]
class LeaveType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $acronym = null;

    #[ORM\Column]
    private ?bool $deductDaysOff = null;

    #[ORM\ManyToMany(targetEntity: Contract::class, mappedBy: 'leaveTypes')]
    private Collection $contracts;

    #[ORM\ManyToMany(targetEntity: LeaveRequest::class, mappedBy: 'type')]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: LeaveRequest::class)]
    private Collection $leaveRequests;

    public function __construct()
    {
        $this->contracts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->leaveRequests = new ArrayCollection();
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

    public function getAcronym(): ?string
    {
        return $this->acronym;
    }

    public function setAcronym(?string $acronym): self
    {
        $this->acronym = $acronym;

        return $this;
    }

    public function isDeductDaysOff(): ?bool
    {
        return $this->deductDaysOff;
    }

    public function setDeductDaysOff(bool $deductDaysOff): self
    {
        $this->deductDaysOff = $deductDaysOff;

        return $this;
    }

    /**
     * @return Collection<int, Contract>
     */
    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function addContract(Contract $contract): self
    {
        if (!$this->contracts->contains($contract)) {
            $this->contracts->add($contract);
            $contract->addLeaveType($this);
        }

        return $this;
    }

    public function removeContract(Contract $contract): self
    {
        if ($this->contracts->removeElement($contract)) {
            $contract->removeLeaveType($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, LeaveRequest>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(LeaveRequest $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->addType($this);
        }

        return $this;
    }

    public function removeComment(LeaveRequest $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            $comment->removeType($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, LeaveRequest>
     */
    public function getLeaveRequests(): Collection
    {
        return $this->leaveRequests;
    }

    public function addLeaveRequest(LeaveRequest $leaveRequest): self
    {
        if (!$this->leaveRequests->contains($leaveRequest)) {
            $this->leaveRequests->add($leaveRequest);
            $leaveRequest->setType($this);
        }

        return $this;
    }

    public function removeLeaveRequest(LeaveRequest $leaveRequest): self
    {
        if ($this->leaveRequests->removeElement($leaveRequest)) {
            // set the owning side to null (unless already changed)
            if ($leaveRequest->getType() === $this) {
                $leaveRequest->setType(null);
            }
        }

        return $this;
    }
}
