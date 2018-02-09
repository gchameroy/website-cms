<?php

namespace AppBundle\Model\GoogleMaps;

/**
 * Class Location
 * @package AppBundle\Model
 */
class Location
{
    /** @var string */
    private $lat;

    /** @var string */
    private $lng;

    /**
     * @param string $lat
     * @return Location
     */
    public function setLat(string $lat): Location
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * @return string
     */
    public function getLat(): string
    {
        return $this->lat;
    }

    /**
     * @param string $lng
     * @return Location
     */
    public function setLng(string $lng): Location
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * @return string
     */
    public function getLng(): string
    {
        return $this->lng;
    }
}
