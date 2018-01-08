<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\DeletableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_offer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserOfferRepository")
 */
class UserOffer
{
    const FORM_NAME = 'product_price_';

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var ProductPrice[]
     * @ORM\OneToMany(targetEntity="ProductPrice", mappedBy="offer", cascade={"remove", "persist"})
     */
    private $prices;

    /**
     * UserOffer constructor.
     */
    public function __construct()
    {
        $this->isDeletable = true;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $label
     * @return UserOffer
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getFormName()
    {
        return self::FORM_NAME . $this->getId();
    }

    /**
     * Add price
     *
     * @param \AppBundle\Entity\ProductPrice $price
     *
     * @return UserOffer
     */
    public function addPrice(\AppBundle\Entity\ProductPrice $price)
    {
        $this->prices[] = $price;

        return $this;
    }

    /**
     * Remove price
     *
     * @param \AppBundle\Entity\ProductPrice $price
     */
    public function removePrice(\AppBundle\Entity\ProductPrice $price)
    {
        $this->prices->removeElement($price);
    }

    /**
     * Get prices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrices()
    {
        return $this->prices;
    }
}
