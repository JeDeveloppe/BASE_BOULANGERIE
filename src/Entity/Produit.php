<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Assert\Regex("/^\d+(\.\d+)?/")
     */
    private $prix;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="produits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isOnLine;

    /**
     * @ORM\Column(type="blob")
     */
    private $imageBlob;

    /**
     * @ORM\OneToMany(targetEntity=ReservationDetails::class, mappedBy="produit", orphanRemoval=true)
     */
    private $reservationDetails;

    public function __construct()
    {
        $this->reservationDetails = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

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

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsOnLine(): ?bool
    {
        return $this->isOnLine;
    }

    public function setIsOnLine(bool $isOnLine): self
    {
        $this->isOnLine = $isOnLine;

        return $this;
    }

    public function getImageBlob()
    {
        return $this->imageBlob;
    }

    public function setImageBlob($imageBlob): self
    {
        $this->imageBlob = $imageBlob;

        return $this;
    }

    /**
     * @return Collection|ReservationDetails[]
     */
    public function getReservationDetails(): Collection
    {
        return $this->reservationDetails;
    }

    public function addReservationDetail(ReservationDetails $reservationDetail): self
    {
        if (!$this->reservationDetails->contains($reservationDetail)) {
            $this->reservationDetails[] = $reservationDetail;
            $reservationDetail->setProduit($this);
        }

        return $this;
    }

    public function removeReservationDetail(ReservationDetails $reservationDetail): self
    {
        if ($this->reservationDetails->removeElement($reservationDetail)) {
            // set the owning side to null (unless already changed)
            if ($reservationDetail->getProduit() === $this) {
                $reservationDetail->setProduit(null);
            }
        }

        return $this;
    }

}
