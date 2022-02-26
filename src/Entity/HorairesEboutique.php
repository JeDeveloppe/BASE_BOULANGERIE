<?php

namespace App\Entity;

use App\Repository\HorairesEboutiqueRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HorairesEboutiqueRepository::class)
 */
class HorairesEboutique
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
    private $day;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex("/^((?:2[0-3]|[01][0-9]):[0-5][0-9])|FERMER$/")
     * @Assert\LessThanOrEqual(propertyPath="fermetureSoir")
     * @Assert\NotBlank
     */
    private $ouvertureMatin;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex("/^((?:2[0-3]|[01][0-9]):[0-5][0-9])|FERMER$/")
     * @Assert\NotBlank
     */
    private $fermetureSoir;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getOuvertureMatin(): ?string
    {
        return $this->ouvertureMatin;
    }

    public function setOuvertureMatin(string $ouvertureMatin): self
    {
        $this->ouvertureMatin = $ouvertureMatin;

        return $this;
    }

    public function getFermetureSoir(): ?string
    {
        return $this->fermetureSoir;
    }

    public function setFermetureSoir(string $fermetureSoir): self
    {
        $this->fermetureSoir = $fermetureSoir;

        return $this;
    }
}
