<?php

use Papi\Client\Exception\NoPredictionException;
use Papi\Client\Model\TraitName;
use Papi\Client\PapiClient;

spl_autoload_register("autoload");

$serviceUrl = "http://api-v2.applymagicsauce.com";
$papiClient = new PapiClient($serviceUrl);

// This is what you got during registration
$customerId = 1;
$apiKey = "apiKey";
// Id of Facebook user
$uid = 1;
// His like ids
$likeIds = array("7010901522", "7721750727", "7557552517", "8536905548", "7723400562",
    "8800570812", "10765693196", "14269799090", "12938634034", "14287253499");

// Authentication. This token will be valid for at least one hour, so you want to store it
// and re-use for further requests
$token = $papiClient->getAuthResource()->requestToken($customerId, $apiKey);

// Get predictions and print
try {
    $prediction = $papiClient->getPredictionResource()->getByLikeIds(
        array(TraitName::BIG5, TraitName::GAY), $token->getTokenString(), $uid, $likeIds);
    print_r($prediction);
} catch (NoPredictionException $e) {
    print "No prediction could be made";
}

function autoload($className)
{
    $className = ltrim($className, "\\");
    $fileName = "";
    if ($lastNsPos = strripos($className, "\\")) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace("\\", DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    require_once $fileName;
}