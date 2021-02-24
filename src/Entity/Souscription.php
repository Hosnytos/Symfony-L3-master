<?php

namespace App\Entity;

use App\Repository\SouscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SouscriptionRepository::class)
 */
class Souscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="relation")
     */
    private $relationUsSo;

    /**
     * @ORM\ManyToOne(targetEntity=Offer::class, inversedBy="relationSoOf")
     */
    private $relation;

    public function __construct(User $user, Offer $offer)
    {
        $this->etat = 'en attente';
        $this->user = $user;
        $this->offer = $offer;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getRelationUsSo(): ?User
    {
        return $this->relationUsSo;
    }

    public function setRelationUsSo(?User $relationUsSo): self
    {
        $this->relationUsSo = $relationUsSo;

        return $this;
    }

    public function getRelation(): ?Offer
    {
        return $this->relation;
    }

    public function setRelation(?Offer $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

}