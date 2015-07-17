<?php

namespace Papi\Client\Exception;

/**
 * Thrown by personality service when prediction cannot be made.
 * This exception should be checked because it represents normal outcome of prediction.
 *
 * @package Papi\Client\Exception
 */
class NoPredictionException extends \Exception
{
}