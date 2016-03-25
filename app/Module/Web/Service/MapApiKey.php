<?php

namespace MP\Module\Web\Service;

/**
 * vraci google mapy api id
 */
class MapApiKey
{
    protected $id;

    /**
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->id = $apiKey;
    }

    /**
     * vraci google mapy api id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
} 
