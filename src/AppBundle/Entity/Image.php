<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Proxies\__CG__\AppBundle\Entity\Newsletter;

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
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="images")
     * @ORM\JoinColumn(nullable=true)
     */
    private $product;


    /**
     * @ORM\OneToMany(targetEntity="Newsletter", mappedBy="imge")
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
     * @param Product $product
     *
     * @return $this
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
     * Set newsletters
     *
     * @param ArrayCollection $newsletters
     */
    public function setNewsletters(ArrayCollection $newsletters)
    {
        $this->newsletters = new ArrayCollection();

        foreach ($newsletters As $newsletter) {
            $this->addAttribute($newsletter);
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
