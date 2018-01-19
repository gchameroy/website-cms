<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cart")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartRepository")
 */
class Cart
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="ordered_at", type="datetime", nullable=true)
     */
    private $orderedAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="carts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="token", type="string", length=60, nullable=true)
     */
    private $token;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="CartProduct", mappedBy="cart")
     */
    private $cartProducts;

    /**
     * Cart constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->cartProducts = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $createdAt
     * @return Cart
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param $orderedAt
     * @return Cart
     */
    public function setOrderedAt($orderedAt)
    {
        $this->orderedAt = $orderedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getOrderedAt()
    {
        return $this->orderedAt;
    }

    /**
     * @param User $user
     * @return Cart
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $token
     * @return Cart
     */
    public function setPassword($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param $token
     * @return Cart
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @param ArrayCollection $cartProducts
     * @return Cart
     */
    public function setCartProducts(ArrayCollection $cartProducts)
    {
        $this->cartProducts = new ArrayCollection();

        foreach ($cartProducts As $cartProduct) {
            $this->addCartProduct($cartProduct);
        }
        
        return $this;
    }

    /**
     * @param CartProduct $cartProduct
     * @return Cart
     */
    public function addCartProduct(CartProduct $cartProduct)
    {
        if (!$this->cartProducts->contains($cartProduct)) {
            $this->cartProducts->add($cartProduct);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCartProducts()
    {
        return $this->cartProducts;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        $length = 0;
        foreach ($this->cartProducts as $cartProduct) {
            $length += $cartProduct->getQuantity();
        }

        return $length;
    }

    /**
     * @param UserOffer|null $offer
     * @return float
     */
    public function getPrice(?UserOffer $offer)
    {
        $price = 0;
        foreach ($this->cartProducts as $cartProduct) {
            $price += $cartProduct->getPrice($offer);
        }

        return $price;
    }
}
