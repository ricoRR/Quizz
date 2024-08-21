<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\DBAL\Types\Types;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $userName = "Anonyme";

    #[ORM\Column(nullable: true)]
    private ?int $result = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $answer = [];

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;





    public function getId(): ?int
    {
        return $this->id;
    }

    public function getuserName(): ?string
    {
        return $this->userName;
    }

    public function getResult(): ?int
    {
        return $this->result;
    }

    public function setuserName(?string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }

    public function setResult(?int $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function getAnswer(): array
    {
        return $this->answer;
    }

    public function setAnswer(array $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

}
