<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OfferRepository::class)
 */
class Offer
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
    private $titre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $textintro;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $textoffre;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prix;

    /**
     * @ORM\OneToMany(targetEntity=Souscription::class, mappedBy="relation")
     */
    private $relationSoOf;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageOffre;

    public function __construct()
    {
        $this->relationSoOf = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getTextintro(): ?string
    {
        return $this->textintro;
    }

    public function setTextintro(?string $textintro): self
    {
        $this->textintro = $textintro;

        return $this;
    }

    public function getTextoffre(): ?string
    {
        return $this->textoffre;
    }

    public function setTextoffre(?string $textoffre): self
    {
        $this->textoffre = $textoffre;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection|Souscription[]
     */
    public function getRelationSoOf(): Collection
    {
        return $this->relationSoOf;
    }

    public function addRelationSoOf(Souscription $relationSoOf): self
    {
        if (!$this->relationSoOf->contains($relationSoOf)) {
            $this->relationSoOf[] = $relationSoOf;
            $relationSoOf->setRelation($this);
        }

        return $this;
    }

    public function removeRelationSoOf(Souscription $relationSoOf): self
    {
        if ($this->relationSoOf->removeElement($relationSoOf)) {
            // set the owning side to null (unless already changed)
            if ($relationSoOf->getRelation() === $this) {
                $relationSoOf->setRelation(null);
            }
        }

        return $this;
    }

    public function getImageOffre(): ?string
    {
        return $this->imageOffre;
    }

    public function setImageOffre(?string $imageOffre): self
    {
        $this->imageOffre = $imageOffre;

        return $this;
    }
}
