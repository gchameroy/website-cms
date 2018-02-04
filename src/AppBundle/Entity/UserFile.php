<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_file")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserFileRepository")
 */
class UserFile
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
     * @var File
     *
     * @ORM\OneToOne(targetEntity="File", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $file;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->file = new File();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param File $file
     *
     * @return UserFile
     */
    public function setFile(File $file): UserFile
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param User $user
     *
     * @return UserFile
     */
    public function setUser(User $user): UserFile
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
