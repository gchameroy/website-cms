<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageRepository")
 */
class Image
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
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var Product
     *
     * @ORM\OneToMany(targetEntity="Product", mappedBy="image")
     */
    private $products;


    /**
     * @ORM\OneToMany(targetEntity="Newsletter", mappedBy="image")
     */
    private $newsletters;

    public function __construct()
    {
        $this->newsletters = new ArrayCollection();
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
     * Set path
     *
     * @param string $path
     *
     * @return Image
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param ArrayCollection $products
     *
     * @return $this
     */
    public function setProducts(ArrayCollection $products)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set newsletters
     *
     * @param ArrayCollection $newsletters
     */
    public function setNewsletters(ArrayCollection $newsletters)
    {
        $this->newsletters = new ArrayCollection();

        foreach ($newsletters As $newsletter) {
            $this->addNewsletter($newsletter);
        }
    }

    /**
     * Add newsletter
     *
     * @param Newsletter $newsletter
     *
     * @return $this
     */
    public function addNewsletter(Newsletter $newsletter)
    {
        if (!$this->newsletters->contains($newsletter)) {
            $this->newsletters->add($newsletter);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getNewsletters()
    {
        return $this->newsletters;
    }
}
