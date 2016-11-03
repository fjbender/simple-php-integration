<?php
include('../Payone.php');

/**
 * @TODO: Define $defaults array() here
 */
$defaults = array(
    "aid" => "your_account_id",
    "mid" => "your_merchant_id",
    "portalid" => "your_portal_id",
    "key" => hash("md5", "your_secret_portal_key"), // the key has to be hashed as md5
    "api_version" => "3.10",
    "mode" => "test", // can be "live" for actual transactions
    "encoding" => "UTF-8"
);

$personalData = array(
    "salutation" => "Herr",
    "title" => "Dr.",
    "firstname" => "Paul",
    "lastname" => "Neverpayer",
    "street" => "Fraunhoferstraße 2-4",
    "addressaddition" => "EG",
    "zip" => "24118",
    "city" => "Kiel",
    "country" => "DE",
    "email" => "paul.neverpayer@payone.de",
    "telephonenumber" => "043125968500",
    "birthday" => "19700204",
    "language" => "de",
    "gender" => "m",
    "ip" => "8.8.8.8"
);

$parameters = array(
    "request" => "authorization",
    "clearingtype" => "sb",             // sb for Online Bank Transfer
    "onlinebanktransfertype" => "PNT",  // PNT for Sofort
    "bankcountry" => "DE",
    //"bankaccount" => "12345678",
    //"bankcode" => "88888888",
    "iban" => "DE85123456782599100003",  // Test data for Sofort
    "bic" => "TESTTEST",
    "amount" => "100000",
    "reference" => uniqid(),
    "narrative_text" => "Just an order",
    "successurl" => "https://yourshop/payment/success?reference=your_unique_reference",
    "errorurl" => "https://yourshop/payment/error?reference=your_unique_reference",
    "backurl" => "https://yourshop/payment/back?reference=your_unique_reference",
);

$request = array_merge($defaults, $parameters, $personalData);
ksort($request);
print_r($request);
/**
 * This should return something like:
 * Array
 * (
 *   [status] => REDIRECT
 *   [redirecturl] => https://www.sofort.com/payment/go/7904xxxxxxxxxxxxxxxxxxxxeeca29ec9d8c7912
 *   [txid] => 211111111
 *   [userid] => 9000000
 * )
 */
$response = Payone::doCurl($request);
print_r($response);


