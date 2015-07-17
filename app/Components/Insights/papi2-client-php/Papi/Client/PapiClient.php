<?php

namespace Papi\Client;

use Papi\Client\Resource\AuthResource;
use Papi\Client\Resource\PredictionResource;

class PapiClient
{
    private $authResource;
    private $predictionResource;

    /**
     * Creates the client
     *
     * @param string $serviceUrl Url to the service
     */
    function __construct($serviceUrl)
    {
        $this->authResource = new AuthResource($serviceUrl . "/auth");
        $this->predictionResource = new PredictionResource($serviceUrl);
    }

    /**
     * Retrieves authentication resource
     *
     * @return \Papi\Client\Resource\AuthResource
     */
    public function getAuthResource()
    {
        return $this->authResource;
    }

    /**
     * Retrieves prediction resource
     *
     * @return \Papi\Client\Resource\PredictionResource
     */
    public function getPredictionResource()
    {
        return $this->predictionResource;
    }

}