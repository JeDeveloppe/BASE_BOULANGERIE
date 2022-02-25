<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InfosLegales
 *
 * @ORM\Table(name="infos_legales")
 * @ORM\Entity
 */
class InfosLegales
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url_site", type="string", length=255, nullable=false)
     */
    private $urlSite;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_societe", type="string", length=255, nullable=false)
     */
    private $nomSociete;

    /**
     * @var string
     *
     * @ORM\Column(name="siret_societe", type="string", length=14, nullable=false)
     */
    private $siretSociete;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_societe", type="string", length=255, nullable=false)
     */
    private $adresseSociete;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_webmaster", type="string", length=255, nullable=false)
     */
    private $nomWebmaster;

    /**
     * @var string
     *
     * @ORM\Column(name="email_site", type="string", length=255, nullable=false)
     */
    private $emailSite;

    /**
     * @var string
     *
     * @ORM\Column(name="societe_webmaster", type="string", length=255, nullable=false)
     */
    private $societeWebmaster;

    /**
     * @var string
     *
     * @ORM\Column(name="hebergeur", type="string", length=255, nullable=false)
     */
    private $hebergeur;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=2)
     */
    private $tva;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrlSite(): ?string
    {
        return $this->urlSite;
    }

    public function setUrlSite(string $urlSite): self
    {
        $this->urlSite = $urlSite;

        return $this;
    }

    public function getNomSociete(): ?string
    {
        return $this->nomSociete;
    }

    public function setNomSociete(string $nomSociete): self
    {
        $this->nomSociete = $nomSociete;

        return $this;
    }

    public function getSiretSociete(): ?string
    {
        return $this->siretSociete;
    }

    public function setSiretSociete(string $siretSociete): self
    {
        $this->siretSociete = $siretSociete;

        return $this;
    }

    public function getAdresseSociete(): ?string
    {
        return $this->adresseSociete;
    }

    public function setAdresseSociete(string $adresseSociete): self
    {
        $this->adresseSociete = $adresseSociete;

        return $this;
    }

    public function getNomWebmaster(): ?string
    {
        return $this->nomWebmaster;
    }

    public function setNomWebmaster(string $nomWebmaster): self
    {
        $this->nomWebmaster = $nomWebmaster;

        return $this;
    }

    public function getEmailSite(): ?string
    {
        return $this->emailSite;
    }

    public function setEmailSite(string $emailSite): self
    {
        $this->emailSite = $emailSite;

        return $this;
    }

    public function getSocieteWebmaster(): ?string
    {
        return $this->societeWebmaster;
    }

    public function setSocieteWebmaster(string $societeWebmaster): self
    {
        $this->societeWebmaster = $societeWebmaster;

        return $this;
    }

    public function getHebergeur(): ?string
    {
        return $this->hebergeur;
    }

    public function setHebergeur(string $hebergeur): self
    {
        $this->hebergeur = $hebergeur;

        return $this;
    }

    public function getTva(): ?string
    {
        return $this->tva;
    }

    public function setTva(string $tva): self
    {
        $this->tva = $tva;

        return $this;
    }


}
