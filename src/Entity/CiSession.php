<?php

namespace App\Entity;

use App\Repository\CiSessionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CiSessionRepository::class)]
#[ORM\Table(name: 'ci_sessions')]
class CiSession
{
    #[ORM\Id]
    #[ORM\Column(length: 128)]
    private ?string $id = null;

    #[ORM\Column(length: 45)]
    private ?string $IpAddress = null;

    #[ORM\Column]
    private ?int $timestamp = null;

    #[ORM\Column(type: Types::BLOB)]
    private $data = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getIpAddress(): ?string
    {
        return $this->IpAddress;
    }

    public function setIpAddress(string $IpAddress): self
    {
        $this->IpAddress = $IpAddress;

        return $this;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }
}
