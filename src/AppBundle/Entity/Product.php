<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * @var string
     * @ORM\Column(name="reference", type="string", length=255, unique=true)
     */
    private $reference;

    /**
     * @var string
     * @ORM\Column(name="variant_name", type="string", length=255)
     */
    private $variantName;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"label"})
     * @ORM\Column(length=128, unique=true, nullable=true)
     */
    private $slug;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(name="more_1", type="string", length=1000, nullable=true)
     */
    private $more1;

    /**
     * @var string
     * @ORM\Column(name="more_2", type="string", length=1000, nullable=true)
     */
    private $more2;

    /**
     * @var string
     * @ORM\Column(name="more_3", type="string", length=1000, nullable=true)
     */
    private $more3;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var string
     * @ORM\Column(name="title_seo", type="string", length=255, nullable=true)
     */
    private $titleSEO;

    /**
     * @var string
     * @ORM\Column(name="description_seo", type="string", length=255, nullable=true)
     */
    private $descriptionSEO;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="variants")
     * @ORM\JoinColumn(nullable=true)
     */
    private $parent;

    /**
     * @var Product[]
     * @ORM\OneToMany(targetEntity="Product", mappedBy="parent", cascade={"remove", "persist"})
     */
    private $variants;

    /**
     * @var Image
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="products")
     * @ORM\JoinColumn(nullable=true)
     */
    private $image;

    /**
     * @var ProductPrice[]
     * @ORM\OneToMany(targetEntity="ProductPrice", mappedBy="product", cascade={"remove", "persist"})
     */
    private $prices;

    /**
     * @var ProductSkill[]
     * @ORM\OneToMany(targetEntity="ProductSkill", mappedBy="product", cascade={"remove", "persist"})
     */
    private $skills;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CartProduct", mappedBy="product", cascade={"remove"})
     */
    private $cartProducts;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="OrderProduct", mappedBy="product")
     */
    private $orderProducts;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->variantName = 'Aucun label';
        $this->variants = new ArrayCollection();
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
        return $this->parent === null ? $this->label : $this->parent->getLabel();
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
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
        return $this->parent === null ? $this->description : $this->parent->getDescription();
    }

    /**
     * @param $more1
     * @return Product
     */
    public function setMore1($more1)
    {
        $this->more1 = $more1;

        return $this;
    }

    /**
     * @return string
     */
    public function getMore1()
    {
        return $this->parent === null ? $this->more1 : $this->parent->getMore1();
    }

    /**
     * @param $more2
     * @return Product
     */
    public function setMore2($more2)
    {
        $this->more2 = $more2;

        return $this;
    }

    /**
     * @return string
     */
    public function getMore2()
    {
        return $this->parent === null ? $this->more2 : $this->parent->getMore2();
    }

    /**
     * @param $more3
     * @return Product
     */
    public function setMore3($more3)
    {
        $this->more3 = $more3;

        return $this;
    }

    /**
     * @return string
     */
    public function getMore3()
    {
        return $this->parent === null ? $this->more3 : $this->parent->getMore3();
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
        return $this->parent === null ? $this->publishedAt : $this->parent->getPublishedAt();
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        $publishedAt =  $this->parent === null ? $this->publishedAt : $this->parent->getPublishedAt();
        return $publishedAt ? true : false;
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
        return $this->parent === null ? $this->category : $this->parent->getCategory();
    }

    /**
     * Set titleSEO
     *
     * @param string $titleSEO
     *
     * @return Product
     */
    public function setTitleSEO($titleSEO)
    {
        $this->titleSEO = $titleSEO;

        return $this;
    }

    /**
     * Get titleSEO
     *
     * @return string
     */
    public function getTitleSEO()
    {
        return $this->parent === null ? $this->titleSEO : $this->parent->getTitleSEO();
    }

    /**
     * Set descriptionSEO
     *
     * @param string $descriptionSEO
     *
     * @return Product
     */
    public function setDescriptionSEO($descriptionSEO)
    {
        $this->descriptionSEO = $descriptionSEO;

        return $this;
    }

    /**
     * Get descriptionSEO
     *
     * @return string
     */
    public function getDescriptionSEO()
    {
        return $this->parent === null ? $this->descriptionSEO : $this->parent->getDescriptionSEO();
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Product
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Product
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Set parent
     *
     * @param Product $parent
     *
     * @return Product
     */
    public function setParent(Product $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Product
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set image
     *
     * @param Image|null $image
     *
     * @return Product
     */
    public function setImage(?Image $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get prices
     *
     * @return ProductPrice[]
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @param UserOffer|null $offer
     * @return float
     */
    public function getPrice(?UserOffer $offer)
    {
        foreach ($this->prices as $price) {
            if ($price->getOffer() === $offer || ($offer === null && $price->getOffer()->getLabel() === 'Sans offre')) {
                return $price->getPrice();
            }
        }

        return 0;
    }

    /**
     * Add skill
     *
     * @param ProductSkill $skill
     *
     * @return Product
     */
    public function addSkill(ProductSkill $skill)
    {
        $this->skills[] = $skill;

        return $this;
    }

    /**
     * Remove skill
     *
     * @param ProductSkill $skill
     */
    public function removeSkill(ProductSkill $skill)
    {
        $this->skills->removeElement($skill);
    }

    /**
     * Get skills
     *
     * @return ProductSkill[]
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * Add variant
     *
     * @param Product $variant
     *
     * @return Product
     */
    public function addVariant(Product $variant)
    {
        $this->variants[] = $variant;

        return $this;
    }

    /**
     * Remove variant
     *
     * @param Product $variant
     */
    public function removeVariant(Product $variant)
    {
        $this->variants->removeElement($variant);
    }

    /**
     * Get variants
     *
     * @return Product[]
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * Set variantName
     *
     * @param string $variantName
     *
     * @return Product
     */
    public function setVariantName($variantName)
    {
        $this->variantName = $variantName;

        return $this;
    }

    /**
     * Get variantName
     *
     * @return string
     */
    public function getVariantName()
    {
        return $this->variantName;
    }

    /**
     * @return ArrayCollection
     */
    public function getCartProducts()
    {
        return $this->cartProducts;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrderProducts()
    {
        return $this->orderProducts;
    }

    public function isDeletable()
    {
        return count($this->orderProducts) > 0 ? false : true;
    }
}
