<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Setting
 *
 * @ORM\Table(name="setting")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingRepository")
 */
class Setting
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="presentation", type="string", length=255)
     */
    private $presentation;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=12)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="titre_seo", type="string", length=255)
     */
    private $titreSeo;

    /**
     * @var string
     *
     * @ORM\Column(name="description_seo", type="text")
     */
    private $descriptionSeo;

    /**
     * @var string
     *
     * @ORM\Column(name="photo_pres", type="string", length=255)
     */
    private $photoPres;

    /**
     * @var string
     *
     * @ORM\Column(name="reseaux_sociaux", type="string", length=255)
     */
    private $reseauxSociaux;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set presentation
     *
     * @param string $presentation
     *
     * @return Setting
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     *
     * @return string
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Setting
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Setting
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set titreSeo
     *
     * @param string $titreSeo
     *
     * @return Setting
     */
    public function setTitreSeo($titreSeo)
    {
        $this->titreSeo = $titreSeo;

        return $this;
    }

    /**
     * Get titreSeo
     *
     * @return string
     */
    public function getTitreSeo()
    {
        return $this->titreSeo;
    }

    /**
     * Set descriptionSeo
     *
     * @param string $descriptionSeo
     *
     * @return Setting
     */
    public function setDescriptionSeo($descriptionSeo)
    {
        $this->descriptionSeo = $descriptionSeo;

        return $this;
    }

    /**
     * Get descriptionSeo
     *
     * @return string
     */
    public function getDescriptionSeo()
    {
        return $this->descriptionSeo;
    }

    /**
     * Set photoPres
     *
     * @param string $photoPres
     *
     * @return Setting
     */
    public function setPhotoPres($photoPres)
    {
        $this->photoPres = $photoPres;

        return $this;
    }

    /**
     * Get photoPres
     *
     * @return string
     */
    public function getPhotoPres()
    {
        return $this->photoPres;
    }

    /**
     * Set reseauxSociaux
     *
     * @param string $reseauxSociaux
     *
     * @return Setting
     */
    public function setReseauxSociaux($reseauxSociaux)
    {
        $this->reseauxSociaux = $reseauxSociaux;

        return $this;
    }

    /**
     * Get reseauxSociaux
     *
     * @return string
     */
    public function getReseauxSociaux()
    {
        return $this->reseauxSociaux;
    }
}

