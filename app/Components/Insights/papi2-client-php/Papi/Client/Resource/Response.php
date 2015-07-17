<?php

namespace Papi\Client\Resource;

class Response
{
    private $_status;
    private $_body;

    /**
     * Creates new instance
     *
     * @param $status
     * @param $body
     * @return \Papi\Client\Resource\Response
     */
    public function __construct($status, $body)
    {
        $this->_status = $status;
        $this->_body = $body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->_status;
    }


}