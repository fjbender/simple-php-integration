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
    "api_version" => "3.8",
    "mode" => "test", // can be "live" for actual transactions
    "encoding" => "UTF-8"
);

$parameters = array(
    "request" => "preauthorization",
    "clearingtype" => "fnc",
    "financingtype" => "PYV",
    "amount" => "100000",
    "reference" => uniqid(),
    "narrative_text" => "A nice and innocent Payolution order",
);
$personalData = array(
    "salutation" => "Herr",
    "title" => "Dr.",
    "firstname" => "Paul",
    "lastname" => "Neverpayer",
    "street" => "Fraunhofer Straße 2-4",
    "addressaddition" => "EG",
    "zip" => "24118",
    "city" => "Kiel",
    "country" => "DE",
    "email" => "paul.neverpayer@payone.de",
    "telephonenumber" => "043125968500",
    "birthday" => "19700204", // a dob is mandatory for Payolution
    "language" => "de",
    "gender" => "m",
    "ip" => "8.8.8.8" // IP address is mandatory for Payolution
);
$articles = array(
    'de[1]' => 'Artikel 1',
    'it[1]' => 'goods',
    'id[1]' => '4711',
    'pr[1]' => '45000',
    'no[1]' => '2',
    'va[1]' => '19',
    'de[2]' => 'Versandkosten',
    'it[2]' => 'shipment',
    'id[2]' => '1234',
    'pr[2]' => '11000',
    'no[2]' => '1',
    'va[2]' => '19',
    'de[3]' => 'Gutschein',
    'it[3]' => 'voucher',
    'id[3]' => 'GUT100',
    'pr[3]' => '-1000',
    'no[3]' => '1',
    'va[3]' => '19',
);

/**
 * Let's do a pre_check to enable Payolution to check the transaction beforehand
 */
$genericPayment = array(
    "add_paydata[action]" => "pre_check",
    "add_paydata[payment_type]" => "Payolution-Invoicing",
    "request" => "genericpayment",
    "clearingtype" => "fnc",
    "financingtype" => "PYV",
    "amount" => "100000"
);

/**
 * The pre_check determines whether we Payolution can fulfill the order
 */
$request = array_merge($defaults, $personalData, $genericPayment);
ksort($request); // just for readability
print_r($request);
$response = Payone::sendRequest($request);
print_r($response);
// @TODO: Handle genericPayment response here and abort order if request is denied

$workorderid["workorderid"] = $response["workorderid"]; // we need to store the workorderid for subsequent requests

/**
 * Let's fire the preauthorization. Remember to include the workorderid to refer to the pre_check!
 */
$request = array_merge($defaults, $parameters, $personalData, $articles, $workorderid);
ksort($request);
print_r($request);
$response = Payone::sendRequest($request);
print_r($response);

echo "Sleeping 3 seconds before capture...";
sleep(3);
echo "done.\n";

/**
 * Send the capture when the package leaves the warehouse to trigger the dunning process.
 */
$parameters = array(
    "request" => "capture",
    "txid" => $response["txid"],
    "amount" => "100000",
    "capturemode" => "completed",
    "sequencenumber" => "1"
);

$request = array_merge($defaults, $parameters);
ksort($request);
print_r($request);
$response = Payone::sendRequest($request);
print_r($response);
