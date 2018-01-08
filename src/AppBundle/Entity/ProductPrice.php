<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPrice
 *
 * @ORM\Table(name="product_price")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductPriceRepository")
 */
class ProductPrice
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
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var UserOffer
     * @ORM\ManyToOne(targetEntity="UserOffer", inversedBy="prices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $offer;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="prices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

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
     * Set price
     *
     * @param float $price
     *
     * @return ProductPrice
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set offer
     *
     * @param UserOffer $offer
     *
     * @return ProductPrice
     */
    public function setOffer(UserOffer $offer)
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * Get offer
     *
     * @return UserOffer
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * Set product
     *
     * @param Product $product
     *
     * @return ProductPrice
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
