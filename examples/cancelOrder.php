<?php
require_once('../Payone.php');
require_once('config.php');


/**
 * It is possible to cancel an order by sending a capture with amount=0
 */
$parameters = array(
    "request" => "capture",
    "amount" => 0, // amount=0 for reversing a preauthorization
    "currency" => "EUR", // currency of the preauthorization
    "txid" => 123456789, // txid of preauthorization
    "settleaccount" => "auto", // see documentation for details on settleaccount parameter
    "capturemode" => "completed" // see documentation for details on capturemode parameter
);

$request = array_merge($defaults, $parameters, $personalData);
ksort($request);
print_r($request);
$response = Payone::sendRequest($request);
print_r($response);
