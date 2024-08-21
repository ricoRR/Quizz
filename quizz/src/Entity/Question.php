<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Categorie;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Reponse;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_categorie = null;

    #[ORM\Column(length: 255)]
    private ?string $question = null;


    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(name: 'id_categorie', referencedColumnName: 'id', nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Reponse::class, cascade: ['persist', 'remove'])]
    private Collection $responses;

    public function __construct()
    {
        $this->responses = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCategorie(): ?int
    {
        return $this->id_categorie;
    }

    public function setIdCategorie(int $id_categorie): static
    {
        $this->id_categorie = $id_categorie;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }


   public function getCategorie(): ?Categorie
   {
       return $this->categorie;
   }

   public function setCategorie(?Categorie $categorie): self
   {
       $this->categorie = $categorie;

       return $this;
   }

   public function getResponses(): Collection
   {
       return $this->responses;
   }

   public function addResponse(Reponse $response): self
   {
       if (!$this->responses->contains($response)) {
           $this->responses[] = $response;
           $response->setQuestion($this); 
       }

       return $this;
   }

   public function removeResponse(Reponse $response): self
   {
       if ($this->responses->removeElement($response)) {
           if ($response->getQuestion() === $this) {
               $response->setQuestion(null);
           }
       }

       return $this;
   }

}
