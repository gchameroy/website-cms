<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @ORM\Table(name="point_of_sale")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PointOfSaleRepository")
 */
class PointOfSale
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
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
     *
     * @ORM\ManyToOne(targetEntity="Address", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $address;

    public function __construct()
    {
        $this->address = new Address();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set website
     *
     * @param string|null $website
     *
     * @return PointOfSale
     */
    public function setWebsite(?string $website): PointOfSale
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string|null $phone
     * @return PointOfSale
     */
    public function setPhone(?string $phone): PointOfSale
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
     * Set address
     *
     * @param Address $address
     *
     * @return PointOfSale
     */
    public function setAddress(Address $address): PointOfSale
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }
}
