<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeliveryZoneRepository")
 */
class DeliveryZone
{
    use SoftDeleteableEntity;

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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $price;

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): DeliveryZone
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setPrice(float $price): DeliveryZone
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }
}
