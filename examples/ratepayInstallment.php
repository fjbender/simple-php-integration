<?php
include('../Payone.php');
require_once 'config.php';
/**
 * Please note:
 * In an actual implementation of RatePay, device fingerprinting has to be performed.
 * The implementation of the device fingerprinting is not part of this example.
 * This script assumes $deviceIdentToken to contain something meaningful
 */

$deviceIdentToken = "b4115d2b8b79c157b09aedb08f91d2980fc7058f692aeb5279ba49aef7737fa211107ad5634a98f70fc44dae1a7c28fc";

/**
 * Please note:
 * RatePay keeps store configs on their site, containing information like currency, basked limits,
 * interest rates and the like. These "profiles" are referenced by a Shop ID, determined by RatePay,
 * and have to be fetched once when setting up RatePay at the shop. This can be performed
 * using the genericpayment[action=profile] request, cf. Server API description for details.
 */

$shopId = "88880103";

/**
 * The decision whether the financing will be calculated by the monthly amount to pay or the total running time
 * of the order has to be queried from the customer using a frontend similar to the example provided by
 * RatePay at: https://www.ratepay.com/ratenrechner/
 * Subsequently, the genericpayment[action=calculation] request has to be fired in the background
 */
$parameters = array(
	"request" => "genericpayment",
	"amount" => "100000",
    'currency' => 'EUR',
	"add_paydata[action]" => "calculation",
	"add_paydata[shop_id]" => $shopId,
	"clearingtype" => "fnc",
	"financingtype" => "RPS",
	"add_paydata[customer_allow_credit_inquiry]" => "yes",
	"add_paydata[calculation_type]" => "calculation-by-rate", // or "calculation-by-time"
	//"add_paydata[month]" => "12" // for calculation-by-time, gives the number of months the credit will be running
	"add_paydata[rate]" => "1000" // the monthly rate the customer is willing to pay
);


$request = array_merge($defaults, $parameters);
ksort($request);
print_r($request);

/**
 * This would happen in the background, e.g. in an AJAX call
 */
$response = Payone::sendRequest($request);

print_r($response);
echo "Sleeping 3 Seconds...\n";
sleep(3);

/**
 * After the customer reached the final step of the checkout and clicks the order button, the preauth is fired
 */
$parameters = array(
	"request" => "preauthorization",
	"add_paydata[shop_id]" => $shopId,
	"add_paydata[debit_paytype]" => "BANK-TRANSFER",
	"add_paydata[installment_amount]" => $response['add_paydata[rate]'],
	"add_paydata[last_installment_amount]" => $response['add_paydata[last-rate]'],
	"add_paydata[installment_number]" => $response['add_paydata[number-of-rates]'],
	"add_paydata[amount]" => $response['add_paydata[total-amount]'],
	"add_paydata[interest_rate]" => $response['add_paydata[interest-rate]'] * 100,		// floats are not accepted here
	"add_paydata[payment_firstday]" => $response['add_paydata[payment-firstday]'],
	"clearingtype" => "fnc",
	"financingtype" => "RPS",
	"amount" => "10000",
    'currency' => 'EUR',
    "add_paydata[customer_allow_credit_inquiry]" => "yes",
	"add_paydata[device_token]" => $deviceIdentToken,
	"reference" => substr($deviceIdentToken, 0, 20),
	//"iban" => "DE00123456871234679800",   // for add_paydata[debit_paytype] => "DIRECT-DEBIT", IBAN and BIC are needed
	//"bic" => "TESTTEST"                   // This paytype means, that instead of wiring the amount to RatePay every
                                            // month, a SEPA Direct Debit is performed on the buyer's account.
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
    "telephonenumber" => "043125968533",
    "birthday" => "19700204",
    "language" => "de",
    "gender" => "m"
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

$request = array_merge($defaults, $parameters, $personalData, $articles);
ksort($request);
print_r($request);
$response = Payone::sendRequest($request);
print_r($response);

/**
 * It might be that RatePay rejected the customer. We will have to inform them about that and the reason:
 */
if ($response["status"] == "ERROR") {
    die ("RatePay has rejected the transaction, the reason given was: " . $response["customermessage"]);
}

echo "Sleeping 3 Seconds...\n";
sleep(3);

/**
 * Send the capture when the package leaves the warehouse to trigger the dunning process.
 */
$parameters = array(
	"request" => "capture",
	"txid" => $response["txid"],
	"amount" => "10000",
    'currency' => 'EUR',
    "capturemode" => "completed",
	"add_paydata[shop_id]" => "88880103"
);

$request = array_merge($defaults, $parameters, $articles);
ksort($request);
print_r($request);
$response = Payone::sendRequest($request);
print_r($response);
