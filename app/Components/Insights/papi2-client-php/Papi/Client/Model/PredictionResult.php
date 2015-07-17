<?php

namespace Papi\Client\Model;

use Papi\Client\Model\Prediction;

class PredictionResult
{
    private $_uid;
    private $_inputUsed;
    private $_predictions;

    /**
     * Creates new instance out of associative array
     *
     * Example:
     *
     * array(
     *      "uid" => 1,
     *      "predictions" => array(
     *          array("trait" => "BIG5_Openness", "value" => 0.75)
     *      )
     * );
     *
     * @param array $predictionArray
     * @return \Papi\Client\Model\PredictionResult
     */
    public static function fromArray(array $predictionArray)
    {
        $prediction = new PredictionResult();
        $prediction->_uid = $predictionArray["uid"];
        $prediction->_inputUsed = $predictionArray["input_used"];
        $prediction->_predictions = self::_createTraits($predictionArray["predictions"]);
        return $prediction;
    }

    private function _createTraits(array $traitsArray)
    {
        $traits[] = array();
        foreach ($traitsArray as $traitArray) {
            $traits[] = Prediction::fromArray($traitArray);
        }
        return $traits;
    }

    /**
     * @param $trait
     * @return \Papi\Client\Model\Prediction
     */
    public function getPrediction($trait)
    {
        if (is_array($this->_predictions)) {
            foreach ($this->_predictions as $prediction) {
                if ($prediction->getTrait() == $trait) {
                    return $prediction;
                }
            }
        }
        return null;
    }

    /**
     * @return int
     */
    public function getInputUsed()
    {
        return $this->_inputUsed;
    }

    /**
     * @return \Papi\Client\Model\Prediction[]
     */
    public function getPredictions()
    {
        return $this->_predictions;
    }

    /**
     * @return int
     */
    public function getUid()
    {
        return $this->_uid;
    }

}