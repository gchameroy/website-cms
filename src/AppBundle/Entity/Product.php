<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
{
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
     * @var string
     * @ORM\Column(name="description", type="string", length=1000)
     */
    private $description;

    /**
     * @var float
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var \DateTime
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    private $category;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Image", mappedBy="product", cascade={"remove", "persist"})
     */
    private $images;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="OrderProduct", mappedBy="product", cascade={"remove", "persist"})
     */
    private $orderProducts;

    /**
     * @ORM\ManyToMany(targetEntity="Attribute", inversedBy="products", cascade={"remove"})
     * @ORM\JoinTable(name="products_attributes")
     */
    private $attributes;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="CartProduct", mappedBy="product", cascade={"remove", "persist"})
     */
    private $cartProducts;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
        $this->attributes = new ArrayCollection();
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
     * @param $label
     * @return Product
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
     * @param $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $price
     * @return Product
     */
    public function setPrice($price)
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

    /**
     * @param $publishedAt
     * @return Product
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return $this->publishedAt ? true : false;
    }

    /**
     * @param Category $category
     * @return Product
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param ArrayCollection $images
     * @return Product
     */
    public function setImages(ArrayCollection $images)
    {
        foreach ($images As $image) {
            $this->addImage($image);
        }

        return $this;
    }

    /**
     * @param Image $image
     * @return Product
     */
    public function addImage(Image $image)
    {
        $image->setProduct($this);
        if (!$this->images->contains($image)) {
            $this->images->add($image);
        }

        return $this;
    }

    /**
     * @param Image $image
     * @return Product
     */
    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param ArrayCollection $orderProducts
     * @return Product
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
     * @return Product
     */
    public function addOrderProduct(OrderProduct $orderProduct)
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->add($orderProduct);
        }

        return $this;
    }

    /**
     * @param OrderProduct $orderProduct
     * @return Product
     */
    public function removeOrderProduct(OrderProduct $orderProduct)
    {
        $this->orderProducts->removeElement($orderProduct);

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
     * Add attribute
     *
     * @param Attribute $attribute
     *
     * @return Product
     */
    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * Remove attribute
     *
     * @param Attribute $attribute
     *
     * @return Product
     */
    public function removeAttribute(Attribute $attribute)
    {
        $this->attributes->removeElement($attribute);

        return $this;
    }

    /**
     * @return Product
     */
    public function removeAttributes()
    {
        $this->attributes->clear();

        return $this;
    }
    /**
     * Get attributes
     *
     * @return ArrayCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }


    /**
     * @param ArrayCollection $cartProducts
     * @return Product
     */
    public function setCartProducts(ArrayCollection $cartProducts)
    {
        foreach ($cartProducts As $cartProduct) {
            $this->addCartProduct($cartProduct);
        }

        return $this;
    }

    /**
     * @param CartProduct $cartProduct
     * @return Product
     */
    public function addCartProduct(CartProduct $cartProduct)
    {
        $this->cartProducts[] = $cartProduct;

        return $this;
    }

    /**
     * @param CartProduct $cartProduct
     * @return Product
     */
    public function removeCartProduct(CartProduct $cartProduct)
    {
        $this->cartProducts->removeElement($cartProduct);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCartProducts()
    {
        return $this->cartProducts;
    }
}
