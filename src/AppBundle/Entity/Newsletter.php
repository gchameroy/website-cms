<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Newsletter
 *
 * @ORM\Table(name="newsletter")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NewsletterRepository")
 */
class Newsletter
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
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @var Image
     * @ORM\OneToOne(targetEntity="Image")
     * @ORM\JoinColumn(nullable=true)
     */
    private $image;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inserted_at", type="datetime")
     */
    private $insertedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="title_seo", type="string", length=255, nullable=true)
     */
    private $titleSEO;

    /**
     * @var string
     *
     * @ORM\Column(name="description_seo", type="string", length=255, nullable=true)
     */
    private $descriptionSEO;

    public function __construct()
    {
        $this->insertedAt = new \DateTime();
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
     * Set title
     *
     * @param string $title
     *
     * @return Newsletter
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Newsletter
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set image
     *
     * @param Image|null $image
     *
     * @return Newsletter
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
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     *
     * @return Newsletter
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
     * Set insertedAt
     *
     * @param \DateTime $insertedAt
     *
     * @return Newsletter
     */
    public function setInsertedAt($insertedAt)
    {
        $this->insertedAt = $insertedAt;

        return $this;
    }

    /**
     * Get insertedAt
     *
     * @return \DateTime
     */
    public function getInsertedAt()
    {
        return $this->insertedAt;
    }

    /**
     * Set titleSEO
     *
     * @param string $titleSEO
     *
     * @return Newsletter
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
        return $this->titleSEO;
    }

    /**
     * Set descriptionSEO
     *
     * @param string $descriptionSEO
     *
     * @return Newsletter
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
        return $this->descriptionSEO;
    }
}
