<?php

namespace AppBundle\Service;

use AppBundle\Model\GoogleMaps\Location;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GoogleMaps
{
    public function geoLocateAddress(string $address): ?Location
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $address = urlencode($address);
        $url = sprintf('https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s', $address, 'AIzaSyCe3T7l6rVotuy_15U_FIK2OCb_3IAdZLM');
        try {
            $response = $serializer->decode(file_get_contents($url), 'json');
        } catch (\Exception $e) {
            $response['status'] = 'KO';
        }

        if ($response['status'] !== 'OK' || count($response['results']) <= 0) {
            return null;
        }

        return $serializer->denormalize($response['results'][0]['geometry']['location'], Location::class);
    }
}
