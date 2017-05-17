<?php
include('../Payone.php');

require_once 'config.php';

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
 * For each Payolution installment, a calculation request has to be performed before the actual transaction
 * is initialized. This request provides a variety of payment plans for the customer to choose from. These
 * are each identified by a different duration, which has to be returned in the preauthorization request
 * in combination with the workorderid identifier of the calculation request.
 */

$genericPayment = array(
    "add_paydata[action]" => "calculation",
    "request" => "genericpayment",
    "clearingtype" => "fnc",
    "financingtype" => "PYS",
    "amount" => "10000",
    'currency' => 'EUR'
);

$request = array_merge($defaults, $personalData, $genericPayment);
ksort($request);
print_r($request);
$response = Payone::sendRequest($request);
print_r($response);

/**
 * After the customer has chosen their desired payment plan, we can save the workorderid for the next request.
 * The selected payment plan is identified by duration, so we have to save that as well, e.g. from the
 * add_paydata[PaymentDetails_1_Duration] parameter.
 */

$workorderid["workorderid"] = $response["workorderid"];

$parameters = array(
    "request" => "preauthorization",
    "clearingtype" => "fnc",
    "financingtype" => "PYS",
    "amount" => "10000",
    'currency' => 'EUR',
    "reference" => uniqid(),
    "add_paydata[installment_duration]" => $response["add_paydata[PaymentDetails_1_Duration]"], // from calculation
);

$articles = array(
    'de[1]' => 'Artikel 1',
    'it[1]' => 'goods',
    'id[1]' => '4711',
    'pr[1]' => '4500',
    'no[1]' => '2',
    'va[1]' => '19',
    'de[2]' => 'Versandkosten',
    'it[2]' => 'shipment',
    'id[2]' => '1234',
    'pr[2]' => '1100',
    'no[2]' => '1',
    'va[2]' => '19',
    'de[3]' => 'Gutschein',
    'it[3]' => 'voucher',
    'id[3]' => 'GUT100',
    'pr[3]' => '-100',
    'no[3]' => '1',
    'va[3]' => '19',
);

$request = array_merge($defaults, $parameters, $personalData, $articles, $workorderid);
ksort($request);
print_r($request);
$response = Payone::sendRequest($request);
print_r($response);

echo "Sleeping 3 seconds before capture...";
sleep(3);
echo "done.\n";

$parameters = array(
    "request" => "capture",
    "txid" => $response["txid"],
    "amount" => "10000",
    'currency' => 'EUR',
    "capturemode" => "completed"
);

/**
 * On both, preauthorization and capture, Payolution will return a reference for the customer. This reference
 * has to be returned to subsequent systems, e.g. to be printed on the invoice or in the confirmation e-mail.
 * It is contained in the parameter clearing_reference.
 */

$request = array_merge($defaults, $parameters);
ksort($request);
print_r($request);
$response = Payone::sendRequest($request);
print_r($response);
