<?php
require_once('../Payone.php');
require_once('config.php');


/**
 * This request is used to manipulate existing user data on the BSPAYONE platform.
 * The primary key for user data is the parameter 'userid', which can be obtained from the response of the preauthorization or authorization request
 * Usecases:
 * - you can overwrite existing values with dummy data to comply with GDPR requests
 * - you can also change the used creditcard for an existing user. Workflow: get a new token with a creditcardcheck over client API --> use updateuser with clearingtype "cc" and the new "pseudocardpan"
 * - If an existing customer changes his or her address, you can change the existing data with this request
 */
$parameters = [
    "request" => "updateuser",
    "userid" => '123456789', // unique userid
    // "customerid" => "12345", // you can also use your own customer identifier if you specified it in the initial payment request
    // "delete_carddata" => "yes", // deletes any stored card data (relevant for GDPR purposes)
    // "delete_bankaccountdata" => "yes" // same as above, only for IBAN/BIC
];

$request = array_merge($defaults, $parameters, $personalData);
ksort($request);
print_r($request);
$response = Payone::sendRequest($request);
print_r($response);
