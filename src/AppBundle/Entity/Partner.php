<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Partner
 *
 * @ORM\Table(name="partner")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PartnerRepository")
 */
class Partner
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
     * @var string|null
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var Address
     * @ORM\ManyToOne(targetEntity="Address", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $address;

    /**
     * @var Image
     * @ORM\ManyToOne(targetEntity="Image", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $image;

    public function __construct()
    {
        $this->address = new Address();
        $this->image = new Image();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string|null $website
     *
     * @return $this
     */
    public function setWebsite(?string $website): Partner
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string|null $phone
     * @return $this
     */
    public function setPhone(?string $phone): Partner
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param Address $address
     *
     * @return Partner
     */
    public function setAddress(Address $address): Partner
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Image $image
     *
     * @return Partner
     */
    public function setImage(Image $image): Partner
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }
}
