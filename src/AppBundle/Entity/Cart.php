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
     * @ORM\Column(name="token", type="string", length=60)
     */
    private $token;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="CartProduct", mappedBy="cart")
     */
    private $products;

    /**
     * Cart constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->products = new ArrayCollection();
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
     * @param ArrayCollection $products
     * @return Cart
     */
    public function setProducts(ArrayCollection $products)
    {
        $this->products = new ArrayCollection();

        foreach ($products As $product) {
            $this->addProduct($product);
        }
        
        return $this;
    }

    /**
     * @param Product $product
     * @return Cart
     */
    public function addProduct(Product $product)
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        $length = 0;
        foreach ($this->products as $product) {
            $length += $product->getQuantity();
        }

        return $length;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        $price = 0;
        foreach ($this->products as $cartProduct) {
            $price += $cartProduct->getPrice();
        }

        return $price;
    }
}
