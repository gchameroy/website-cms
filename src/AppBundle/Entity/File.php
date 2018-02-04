<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * @ORM\Table(name="file")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FileRepository")
 */
class File
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
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="extension", type="string", length=255)
     */
    private $extension;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $title
     *
     * @return File
     */
    public function setTitle(string $title): File
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $path
     *
     * @return File
     */
    public function setPath($path): File
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $extension
     *
     * @return File
     */
    public function setExtension($extension): File
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        switch ($this->extension) {
            case 'pdf':
                return 'fa-file-pdf-o';
            case 'doc':
            case 'docx':
                return 'fa-file-word-o';
            default:
                return 'fa-file';
        }
    }
}
