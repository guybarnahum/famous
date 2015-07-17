<?php

namespace Papi\Client\Resource;

use Papi\Client\Exception\NotAuthenticatedException;
use Papi\Client\Exception\PapiClientException;
use Papi\Client\Model\PapiToken;

class AuthResource
{
    private $_url;

    /**
     * Creates the resource
     *
     * @param string $url Url to the resource
     */
    function __construct($url)
    {
        $this->_url = $url;
    }

    /**
     * Requests access token
     *
     * @param int $customerId Customer id
     * @param string $apiKey API key
     * @return PapiToken
     * @throws \Papi\Client\Exception\NotAuthenticatedException
     * @throws \Papi\Client\Exception\PapiClientException
     */
    function requestToken($customerId, $apiKey)
    {
        $response = $this->_getTokenResponse($this->_getTokenRequestBody($customerId, $apiKey));
        if ($response->getStatus() == 200) {
            return PapiToken::fromArray(json_decode($response->getBody(), true));
        } else if ($response->getStatus() == 401) {
            throw new NotAuthenticatedException("CustomerId and apiKey don't match");
        } else {
            throw new PapiClientException("Received unexpected response status code = " . $response->getStatus());
        }
    }

    private function _getTokenRequestBody($customerId, $apiKey)
    {
        return "{ \"customer_id\": $customerId, \"api_key\": \"$apiKey\" }";
    }

    private function _getTokenResponse($json)
    {
        $curl = curl_init($this->_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Content-Length: " . strlen($json)
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        return new Response($status, $body);
    }

}