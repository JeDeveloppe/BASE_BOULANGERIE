<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaiementRepository::class)
 */
class Paiement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $paiementToken;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, mappedBy="paiement", cascade={"persist", "remove"})
     */
    private $document;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaiementToken(): ?string
    {
        return $this->paiementToken;
    }

    public function setPaiementToken(string $paiementToken): self
    {
        $this->paiementToken = $paiementToken;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): self
    {
        // unset the owning side of the relation if necessary
        if ($document === null && $this->document !== null) {
            $this->document->setPaiement(null);
        }

        // set the owning side of the relation if necessary
        if ($document !== null && $document->getPaiement() !== $this) {
            $document->setPaiement($this);
        }

        $this->document = $document;

        return $this;
    }

}
