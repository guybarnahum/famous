<?php

namespace Papi\Client\Resource;

use Papi\Client\Exception\NoPredictionException;
use Papi\Client\Exception\PapiClientException;
use Papi\Client\Exception\TokenExpiredException;
use Papi\Client\Exception\TokenInvalidException;
use Papi\Client\Exception\UsageLimitExceededException;
use Papi\Client\Model\PredictionResult;

class PredictionResource
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
     * Does prediction for selected traits based on user's Facebook Like IDs
     *
     * @param array $traits Array of traits to be predicted
     * @param string $token Access token
     * @param int $uid User's Facebook ID
     * @param array $likeIds Array of user's Facebook Like IDs
     * @return PredictionResult prediction for requested traits
     * @throws \Papi\Client\Exception\TokenExpiredException
     * @throws \Papi\Client\Exception\TokenInvalidException
     * @throws \Papi\Client\Exception\UsageLimitExceededException
     * @throws \Papi\Client\Exception\NoPredictionException
     * @throws \Papi\Client\Exception\PapiClientException
     */
    function getByLikeIds(array $traits, $token, $uid, array $likeIds)
    {
        $response = $this->_getByLikeIdsResponse($this->_url . "/like_ids", $traits, $token, $uid, json_encode($likeIds));
        if ($response->getStatus() == 200) {
            return PredictionResult::fromArray(json_decode($response->getBody(), true));
        } else if ($response->getStatus() == 401) {
            throw new TokenInvalidException("Access token is invalid");
        } else if ($response->getStatus() == 403) {
            throw new TokenExpiredException("Access token has expired");
        } else if ($response->getStatus() == 403) {
            throw new UsageLimitExceededException("Usage limit was exceeded");
        } else if ($response->getStatus() == 204) {
            throw new NoPredictionException("Couldn't make prediction based on those like ids");
        } else {
            throw new PapiClientException("Received unexpected response status code = " . $response->getStatus());
        }
    }

    private function _getByLikeIdsResponse($url, $traits, $token, $uid, $json)
    {
        $curl = curl_init($url . "?" . $this->_getByLikeIdsUrlParameters($traits, $uid));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "X-Auth-Token: " . $token,
            "Content-Length: " . strlen($json)
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        return new Response($status, $body);
    }

    private function _getByLikeIdsUrlParameters($traits, $uid)
    {
        $data = array();
        if ($traits != null && !empty($traits)) {
            $data["traits"] = implode(",", $traits);
        }
        if ($uid != null) {
            $data["uid"] = $uid;
        }
        return http_build_query($data);
    }

}