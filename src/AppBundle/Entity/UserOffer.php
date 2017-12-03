<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\DeletableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_offer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserOfferRepository")
 */
class UserOffer
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
     * UserOffer constructor.
     */
    public function __construct()
    {
        $this->isDeletable = true;
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
     * @return UserOffer
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
}
