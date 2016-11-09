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
    "clearingtype" => "wlt", // wallet clearing type
    "wallettype" => "PPE", // PPE for Paypal
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
 *  [status] => REDIRECT
 *  [redirecturl] => https://www.sandbox.paypal.com/webscr?useraction=commit&cmd=_express-checkout&token=EC-4XXX73XXXK03XXX1A
 *  [txid] => 205387102
 *  [userid] => 90737467
 * )
 */
$response = Payone::sendRequest($request);
print_r($response);
