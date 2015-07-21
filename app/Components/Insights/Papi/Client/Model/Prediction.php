<?php

namespace Papi\Client\Model;

class Prediction
{
    private $_trait;
    private $_value;

    /**
     * Creates new instance out of associative array
     *
     * Example:
     *
     * array("trait" => "BIG5_Openness", "value" => 0.75);
     *
     * @param array $traitArray
     * @return \Papi\Client\Model\Prediction
     */
    public static function fromArray(array $traitArray)
    {
        $trait = new Prediction();
        $trait->_trait = $traitArray["trait"];
        $trait->_value = $traitArray["value"];
        return $trait;
    }

    /**
     * @return string
     */
    public function getTrait()
    {
        return $this->_trait;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->_value;
    }

}