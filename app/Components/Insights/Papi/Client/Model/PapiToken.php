<?php

namespace Papi\Client\Model;

class PapiToken
{
    private $_tokenString;
    private $_expires;

    /**
     * Creates new instance out of associative array
     *
     * Example:
     *
     * array(
     *      "token" => "token",
     *      "expires" => 12345678
     * );
     *
     * @param array $tokenArray
     * @return \Papi\Client\Model\PapiToken
     */
    public static function fromArray(array $tokenArray)
    {
        $token = new PapiToken();
        $token->_tokenString = $tokenArray["token"];
        $token->_expires = $tokenArray["expires"];
        return $token;
    }

    /**
     * @return int
     */
    public function getExpires()
    {
        return $this->_expires;
    }

    /**
     * @return string
     */
    public function getTokenString()
    {
        return $this->_tokenString;
    }

}