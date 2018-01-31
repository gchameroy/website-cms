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
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255)
     */
    private $website;

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
     * @param string $website
     *
     * @return PointOfSale
     */
    public function setWebsite(string $website): PointOfSale
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
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
