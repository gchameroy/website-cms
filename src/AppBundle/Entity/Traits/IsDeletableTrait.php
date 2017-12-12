<?php

namespace AppBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IsDeletableTrait
{
    /**
     * @var bool
     * @ORM\Column(name="is_deletable", type="boolean")
     */
    private $isDeletable;

    /**
     * @param $isDeletable
     * @return $this
     */
    public function setIsDeletable($isDeletable)
    {
        $this->isDeletable = $isDeletable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeletable()
    {
        return $this->isDeletable;
    }
}
