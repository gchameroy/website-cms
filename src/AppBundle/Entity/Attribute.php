<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute
 *
 * @ORM\Table(name="attribute")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AttributeRepository")
 */
class Attribute
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
     * @var CategoryAttribute
     *
     * @ORM\ManyToOne(targetEntity="CategoryAttribute", inversedBy="attributes")
     * @ORM\JoinColumn(name="category_attribute_id", referencedColumnName="id", nullable=false)
     */
    private $categoryAttribute;

    /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="attributes", cascade={"remove"})
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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
     * @return Attribute
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
     * @param CategoryAttribute $categoryAttribute
     *
     * @return $this
     */
    public function setCategoryAttribute(CategoryAttribute $categoryAttribute)
    {
        $this->categoryAttribute = $categoryAttribute;

        return $this;
    }

    /**
     * @return CategoryAttribute
     */
    public function getCategoryAttribute()
    {
        return $this->categoryAttribute;
    }

    /**
     * Add product
     *
     * @param Product $product
     *
     * @return Attribute
     */
    public function addProduct(Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param Product $product
     *
     * @return $this
     */
    public function removeProduct(Product $product)
    {
        $this->products->removeElement($product);

        return $this;
    }

    /**
     * Get products
     *
     * @return ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
