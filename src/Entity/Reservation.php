<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, mappedBy="reservation", cascade={"persist", "remove"})
     */
    private $document;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\OneToMany(targetEntity=ReservationDetails::class, mappedBy="reservation", orphanRemoval=true)
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(Document $document): self
    {
        // set the owning side of the relation if necessary
        if ($document->getReservation() !== $this) {
            $document->setReservation($this);
        }

        $this->document = $document;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

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
            $reservationDetail->setReservation($this);
        }

        return $this;
    }

    public function removeReservationDetail(ReservationDetails $reservationDetail): self
    {
        if ($this->reservationDetails->removeElement($reservationDetail)) {
            // set the owning side to null (unless already changed)
            if ($reservationDetail->getReservation() === $this) {
                $reservationDetail->setReservation(null);
            }
        }

        return $this;
    }
}
