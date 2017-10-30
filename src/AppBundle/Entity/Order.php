<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderRepository")
 */
class Order
{
    const STATUS_IN_PREPARE = 1;
    const STATUS_SENT = 2;
    const STATUS_CANCELED = 3;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @var boolean
     * @ORM\Column(name="is_paid", type="boolean")
     */
    private $isPaid;

    /**
     * @var int
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="orders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Address
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumn(name="delivery_address_id", referencedColumnName="id")
     */
    private $deliveryAddress;

    /**
     * @var Address
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumn(name="billing_address_id", referencedColumnName="id")
     */
    private $billingAddress;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="OrderProduct", mappedBy="order", cascade={"remove", "persist"})
     */
    private $orderProducts;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->orderProducts = new ArrayCollection();
        $this->isPaid = false;
        $this->status = self::IN_PREPARE;
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
     * @return Order
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
     * @param User $user
     * @return Order
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
     * @param Address $deliveryAddress
     * @return Order
     */
    public function setDeliveryAddress(Address $deliveryAddress)
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    /**
     * @return Address
     */
    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }

    /**
     * @param Address $billingAddress
     * @return Order
     */
    public function setBillingAddress(Address $billingAddress)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * @return Address
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param ArrayCollection $orderProducts
     * @return Order
     */
    public function setOrderProducts(ArrayCollection $orderProducts)
    {
        $this->orderProducts = new ArrayCollection();

        foreach ($orderProducts As $orderProduct) {
            $this->addOrderProduct($orderProduct);
        }
        
        return $this;
    }

    /**
     * @param OrderProduct $orderProduct
     * @return Order
     */
    public function addOrderProduct(OrderProduct $orderProduct)
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->add($orderProduct);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrderProducts()
    {
        return $this->orderProducts;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        $length = 0;
        foreach ($this->orderProducts as $orderProduct) {
            $length += $orderProduct->getQuantity();
        }

        return $length;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        $price = 0;
        foreach ($this->orderProducts as $orderProduct) {
            $price += $orderProduct->getPrice();
        }

        return $price;
    }

    /**
     * Set comment
     * @param string $comment
     * @return Order
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        $total = 0;
        foreach ($this->orderProducts as $orderProduct) {
            $total += $orderProduct->getTotal();
        }

        return $total;
    }

    /**
     * Set isPaid
     *
     * @param boolean $isPaid
     *
     * @return Order
     */
    public function setIsPaid($isPaid)
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    /**
     * Get isPaid
     *
     * @return boolean
     */
    public function getIsPaid()
    {
        return $this->isPaid;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Remove orderProduct
     *
     * @param \AppBundle\Entity\OrderProduct $orderProduct
     */
    public function removeOrderProduct(\AppBundle\Entity\OrderProduct $orderProduct)
    {
        $this->orderProducts->removeElement($orderProduct);
    }
}
