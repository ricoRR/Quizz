<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Categorie;
use App\Entity\Question;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
#[ORM\Table(name: '`reponse`')]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_question = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reponse = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $reponse_expected = null;

    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'reponses')]
    #[ORM\JoinColumn(name: 'id_question', referencedColumnName: 'id', nullable: false)]
    private ?Question $question = null;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getIdQuestion(): ?int
    {
        return $this->id_question;
    }

    public function setIdQuestion(int $id_question): static
    {
        $this->id_question = $id_question;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): static
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getReponseExpected(): ?bool
    {
        return $this->reponse_expected;
    }

    public function setReponseExpected(bool $reponse_expected): static
    {
        $this->reponse_expected = $reponse_expected;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
