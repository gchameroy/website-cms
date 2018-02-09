<?php

namespace AppBundle\Service;

use AppBundle\Model\GoogleMaps\Location;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class GoogleMaps
 */
class GoogleMaps
{
    /**
     * @param string $address
     * @return Location
     */
    public function geoLocateAddress(string $address): Location
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $address = urlencode($address);
        $url = sprintf('https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s', $address, 'AIzaSyCe3T7l6rVotuy_15U_FIK2OCb_3IAdZLM');
        $response = $serializer->decode(file_get_contents($url), 'json');

        if ($response['status'] !== 'OK' || count($response['results']) <= 0) {
            return new Location();
        }

        return $serializer->denormalize($response['results'][0]['geometry']['location'], Location::class);
    }
}
