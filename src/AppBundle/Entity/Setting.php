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
     * @ORM\Column(name="presentation", type="text")
     */
    private $presentation;

    /**
     * @var Address
     * @ORM\ManyToOne(targetEntity="Address", cascade={"remove", "persist"})
     * @ORM\JoinColumn(name="adress", referencedColumnName="id")
     */
    private $adress;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="title_seo", type="string", length=255)
     */
    private $titleSeo;

    /**
     * @var string
     *
     * @ORM\Column(name="description_seo", type="text")
     */
    private $descriptionSeo;

     /**
     * @var Image
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="setting")
     * @ORM\JoinColumn(name="photo_pres", referencedColumnName="id", nullable=true)
     */
    private $photoPres;

    /**
     * @var string
     *
     * @ORM\Column(name="social_network", type="string", length=255)
     */
    private $socialNetwork;


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
     * Set adress
     *
     * @param Address $adress
     * @return Setting
     */
    public function setAdress(Adress $adress)
    {
        $this->adress = $adress;

        return $this;
    }

    /**
     * Get adress
     *
     * @return Adress
     */
    public function getAdress()
    {
        return $this->adress;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Setting
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set titleSeo
     *
     * @param string $titleSeo
     *
     * @return Setting
     */
    public function setTitleSeo($titleSeo)
    {
        $this->titleSeo = $titleSeo;

        return $this;
    }

    /**
     * Get titleSeo
     *
     * @return string
     */
    public function getTitleSeo()
    {
        return $this->titleSeo;
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
     * @param Image $photoPres
     *
     * @return Setting
     */
    public function setPhotoPres(Image $photoPres)
    {
        $this->photoPres = $photoPres;

        return $this;
    }

    /**
     * Set image
     *
     * @return Setting
     */
    public function removePhotoPres()
    {
        $this->photoPres = null;

        return $this;
    }

    /**
     * Get photoPres
     *
     * @return Image
     */
    public function getPhotoPres()
    {
        return $this->photoPres;
    }

    /**
     * Set socialNetwork
     *
     * @param string $socialNetwork
     *
     * @return Setting
     */
    public function setSocialNetwork($socialNetwork)
    {
        $this->socialNetwork = $socialNetwork;

        return $this;
    }

    /**
     * Get socialNetwork
     *
     * @return string
     */
    public function getSocialNetwork()
    {
        return $this->socialNetwork;
    }
}

