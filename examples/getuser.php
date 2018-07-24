<?php
require_once('../Payone.php');
require_once('config.php');


/**
 * This request is used to retrieve existing user data on the BSPAYONE platform.
 * The primary key for user data is the parameter 'userid', which can be obtained from the response of the
 * preauthorization or authorization request
 * Usecases:
 * - use this request to receive structured JSON containing personal data
 * - you can also retrieve usertokens to migrate user data between merchant accounts
 * PLEASE NOTE: Like payment requests, we will charge a fee for each of these requests.
 * Relying on these will get expensive very fast.
 */
$parameters = [
    "request" => "getuser",
    "userid" => '123456789', // unique userid
    "type" => 'userdata', // retrieves user data as structured JSON
    // "customerid" => "12345", // you can also use your own customer identifier if it was specified earlier
    // "getusertoken" => "yes", // returns a token for migration purposes.
    // You can then use a payment request (preauthorization or authorization) on another mid to migrate the user data
];

$request = array_merge($defaults, $parameters);
ksort($request);
print_r($request);
$response = Payone::sendRequest($request,"json");
print_r($response);
/*This should return something like this (yes, structured JSON):
{
    "Status": "OK",
    "UserId": "89668301",
    "Person": {
        "Salutation": "Herr",
        "Title": "Dr.",
        "DateOfBirth": "19881231",
        "PersonalId": "811218+987-6",
        "LanguageCode": "de",
        "LanguageName": "German",
        "FirstName": "Max",
        "LastName": "Mustermann"
        },
    "Address": {
        "CountryCode": "DE",
        "CountryName": "Deutschland",
        "Street": "Fraunhoferstr. 2-4",
        "Zip": "24118",
        "City": "Kiel"
        },
    "Company": {
        "CompanyName": "Musterfirma GmbH"
        },
    "ContactData": {
        "Phone": "+49 431 25968-0",
        "Mail": "max.mustermann@bspayone.com"
        },
    "BankAccount": {
        "Iban": "DE26210700240444444444",
        "Bic": "TESTTEST",
        "BankAccountHolder": "Mustermann, Max",
        "BankCountryCode": "DE",
        "BankCountryName": "Deutschland"
        },
        "CreditCardData": {
        "PseudoCardPan": "9410010000002325942",
        "CardType": "V",
        "CardExpireDate": "1812",
        "MaskedCardPan": "411111xxxxxx1111"
        }
}*/
