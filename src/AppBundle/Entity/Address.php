<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AddressRepository")
 */
class Address
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
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(name="company", type="string", length=255, nullable=true)
     */
    private $company;

    /**
     * @var string
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string
     * @ORM\Column(name="zip_code", type="string", length=255)
     */
    private $zipCode;

    /**
     * @var string
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string
     * @ORM\Column(name="country", type="string", length=255)
     */
    private $country;

    /**
     * @var string
     * @ORM\Column(name="lat", type="string", length=255, nullable=true)
     */
    private $lat;

    /**
     * @var string
     * @ORM\Column(name="lng", type="string", length=255, nullable=true)
     */
    private $lng;

    /**
     * @var DeliveryZone
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\DeliveryZone")
     * @ORM\JoinColumn(nullable=true)
     */
    private $deliveryZone;

    public function __construct()
    {
        $this->country = 'France';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param $company
     * @return $this
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $zipCode
     * @return Address
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param string $city
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $country
     * @return Address
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $lat
     * @return Address
     */
    public function setLat(string $lat): Address
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
     * @return Address
     */
    public function setLng(string $lng): Address
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

    public function setDeliveryZone(?DeliveryZone $deliveryZone): Address
    {
        $this->deliveryZone = $deliveryZone;

        return $this;
    }

    public function getDeliveryZone(): ?DeliveryZone
    {
        return $this->deliveryZone;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->lastName.' '.$this->firstName;
    }

    /**
     * @return string
     */
    public function getFormattedAddress()
    {
        return sprintf(
            '%s, %s %s, %s',
            $this->address,
            $this->zipCode,
            $this->city,
            $this->country
        );
    }

    /**
     * @return string|null
     */
    public function getPosition(): ?string
    {
        if ($this->lat === null || $this->lng === null) {
            return null;
        }

        return sprintf('%s,%s', $this->lat, $this->lng);
    }
}
