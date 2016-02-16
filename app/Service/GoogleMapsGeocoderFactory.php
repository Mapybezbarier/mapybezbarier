<?php


namespace MP\Service;


/**
 * Tovarna na sluzbu pro GoogleMapsGeocoder
 */
class GoogleMapsGeocoderFactory
{
    protected $apiKey;

    /**
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Vytvori instanci sluzby pro geocoding
     *
     * @return \GoogleMapsGeocoder
     */
    public function create()
    {
        $geocoder = new \GoogleMapsGeocoder();
        $geocoder->setApiKey($this->apiKey);
        $geocoder->setRegion('cz'); // hledam pouze v CR

        return $geocoder;
    }
} 
