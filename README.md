# checkmobi-php

Requirements

php-curl
php-openssl

Or instead curl you can use HTTP_Request2

pear package HTTP_Request2

Detailed documentation : https://checkmobi.com/documentation.html

Basic Usage (check documentation for all available customizations):

$api = new CheckMobiRest("secret key here");

//checking a number for being valid

$response = $api->CheckNumber(array("number" => "number here"));

//get list of countries & flags

$response = $api->GetCountriesList();

//validate a number using "Missed call method". (type can be : sms, ivr, cli, reverse_cli)

$response = $api->RequestValidation(array("type" => "reverse_cli", "number" => "number_here"));

//verify a pin for a certain request

$response = $api->VerifyPin(array("id" => "request id here", "pin" => "5659"));

//check validation status for a certain request

$response = $api->ValidationStatus(array("id" => "request id here"));

//send a custom sms

$response = $api->SendSMS(array("to" => "number here", "text" => "message here"));


