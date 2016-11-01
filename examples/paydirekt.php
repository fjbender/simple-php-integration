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

/**
 * Paydirekt requires both, personal data and shipping data
 */
$shippingData = array(
    "shipping_firstname" => "Paul",
    "shipping_lastname" => "Neverpayer",
    "shipping_street" => "Hamburger Allee 26-28",
    "shipping_zip" => "60486",
    "shipping_city" => "Frankfurt am Main",
    "shipping_country" => "DE"
);

$parameters = array(
    "request" => "authorization",
    "clearingtype" => "wlt", // wallet clearing type
    "wallettype" => "PDT", // PDT for Paydirekt
    "amount" => "100000",
    "reference" => uniqid(),
    "narrative_text" => "Just an order",
    "successurl" => "https://yourshop/payment/success?reference=your_unique_reference",
    "errorurl" => "https://yourshop/payment/error?reference=your_unique_reference",
    "backurl" => "https://yourshop/payment/back?reference=your_unique_reference",
    /**
     * These are specific Paydirekt parameters. Paydirekt can verify the age of the customer.
     * If it's below the add_paydata[minimum_age], the payment will be refused and the customer
     * will be redirected to the URL defined in add_paydata[redirect_url_after_age_verification_failure]
     */
    "add_paydata[minimum_age]" => "18",
    "add_paydata[redirect_url_after_age_verification_failure]" => "https://yourshop/payment/tooyoung"
);

$request = array_merge($defaults, $parameters, $personalData, $shippingData);
ksort($request);
print_r($request);
/**
 * This should return something like:
 * Array
 * (
 *   [status] => REDIRECT
 *   [redirecturl] => https://sandbox.paydirekt.de/checkout/#/checkout/fe012345-abcd-efef-1234-7d7d7d7d7d7d
 *   [txid] => 211111111
 *   [userid] => 9000000
 * )
 */
$response = Payone::doCurl($request);
print_r($response);
