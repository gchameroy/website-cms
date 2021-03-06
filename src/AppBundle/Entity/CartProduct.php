<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cart_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartProductRepository")
 */
class CartProduct
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var Cart
     * @ORM\ManyToOne(targetEntity="Cart", inversedBy="cartProducts")
     * @ORM\JoinColumn(name="cart_id", referencedColumnName="id")
     */
    private $cart;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="cartProducts")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * CartProduct constructor.
     */
    public function __construct()
    {
        $this->quantity = 1;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $quantity
     * @return CartProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param Cart $cart
     * @return CartProduct
     */
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param Product $product
     * @return CartProduct
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param UserOffer|null $offer
     * @return float
     */
    public function getPrice(?UserOffer $offer)
    {
        return $this->product->getPrice($offer) * $this->quantity;
    }
}
