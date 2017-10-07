<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
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
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="product", cascade={"remove", "persist"})
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="OrderProduct", mappedBy="product", cascade={"remove", "persist"})
     */
    private $orderProducts;

    /**
     * @ORM\ManyToMany(targetEntity="Attribute", inversedBy="products", cascade={"remove"})
     * @ORM\JoinTable(name="products_attributes")
     */
    private $attributes;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }

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
     * Set label
     *
     * @param string $label
     *
     * @return Product
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Product
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
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     *
     * @return Product
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function isPublished()
    {
        return $this->publishedAt ? true : false;
    }

    /**
     * @param Category $category
     *
     * @return $this
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
     *
     * @return $this
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
     *
     * @return $this
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
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param ArrayCollection $orderProducts
     */
    public function setOrderProducts(ArrayCollection $orderProducts)
    {
        $this->orderProducts = new ArrayCollection();

        foreach ($orderProducts As $orderProduct) {
            $this->addOrderProduct($orderProduct);
        }
    }

    /**
     * @param OrderProduct $orderProduct
     *
     * @return $this
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
     * Remove image
     *
     * @param Image $image
     *
     * @return Product
     */
    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);

        return $this;
    }

    /**
     * Remove orderProduct
     *
     * @param \AppBundle\Entity\OrderProduct $orderProduct
     *
     * @return Product
     */
    public function removeOrderProduct(OrderProduct $orderProduct)
    {
        $this->orderProducts->removeElement($orderProduct);

        return $this;
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
}
