# checkmobi-php

The official SDK for integrating with the [CheckMobi API][1]. CheckMobi is a service that provides affordable services like: 
- Two-factor authentication via SMS, Missed Call, and IVR
- SMS API for marketing and transactional notifications.
- Voice API for creating custom IVRs.

This PHP SDK makes it easy for developers to add CheckMobi's features like SMS and Voice to their PHP apps without much hassle.

## Requirements

In order to use the library you need to have available one of `CURL` or `HTTP_Request2` extension:

### Using `CURL`

```
php-curl
php-openssl
```

### Using `HTTP_Request2` :

```
pear install HTTP_Request2
```

The SDK initially checks for the `CURL` extension and then falls back to `HTTP_Request2` if necessary. You can set the transport method using the constructor's `options` parameter.

## Installation

The SDK can be installed using `Composer`:

```sh
composer require  checkmobi/checkmobi-php
```

## Get started

### Create the CheckMobiRest client

```php
use checkmobi\CheckMobiRest;
$client = new CheckMobiRest("secret key here");
```

In case you want to change the default behaviours of the library you can use the `options` array properties:

| Property       | Default                     | Description                                                                                                                                                                                                                                                                                                                                      |
|----------------|-----------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| api.base_url   | https://api.checkmobi.com   | API endpoint.                                                                                                                                                                                                                                                                                                                                    |
| api.version    | v1                          | API endpoint version.                                                                                                                                                                                                                                                                                                                            |
| net.transport  | `RequestInterface::HANDLER_DEFAULT` | Transport engine: `RequestInterface::HANDLER_DEFAULT` - will try to use `CURL` if available otherwise fallbacks to `HTTP_Request2`, `RequestInterface::HANDLER_CURL` will force to use CURL, if fails will trigger an exception, `RequestInterface::HANDLER_HTTP2` will force `HTTP_Request2` instantiation, if fails will trigger an exception. |
| net.timeout    | 30                          | Connection and request timeout in seconds.                                                                                                                                                                                                                                                                                                       |
| net.ssl_verify_peer| true                    | Indicates if the server certificate is verified or not before transmitting any data.                                                                                                                                                                                                                                                             |

#### Example

```php
$client = new CheckMobiRest("secret key here", [
    "net.timeout" => 10, 
    "net.ssl_verify_peer" => false
]);
```

### Resources

The SDK is a wrapper over the REST API described [here][1]. For all properties accepted by the following methods check [the documentation][1].

```php

// get list of countries & flags

$response = $client->GetCountriesList();

// get account details

$response = $client->GetAccountDetails();

// get prefixes

$response = $client->GetPrefixes();

// checking a number for being valid

$response = $client->CheckNumber(array("number" => "+number_here"));

// validate a number using "Missed call method". (type can be : sms, ivr, reverse_cli)

$response = $client->RequestValidation(array("type" => "reverse_cli", "number" => "+number_here"));

// verify a pin for a certain request

$response = $client->VerifyPin(array("id" => "request id here", "pin" => "5659"));

// check validation status for a certain request

$response = $client->ValidationStatus(array("id" => "request id here"));

// get remote config profile

$response = $client->GetRemoteConfigProfile(array("number" => "+number_here", "platform" => "android"));

// send a custom sms

$response = $client->SendSMS(array("to" => "+number_here", "text" => "message here"));

// get sms details

$response = $client->GetSmsDetails(array("id" => "sms id here"));

// place call

$params = [
    "from" => "+source_number_here", 
    "to" => "+destination_number_here", 
    "events" => [
        ["action" => "speak", "text" => "Hello world", "loop" => 2, "language" => "en-US"]
    ]
];
$response = $client->PlaceCall($params);

// get call details

$response = $client->GetCallDetails(array("id" => "call id here"));

// hangup call

$response = $client->HangUpCall(array("id" => "call id here"));

// perform HLR Lookup

$response = $client->HLRLookup(["number"=> "+number here"]);

// perform MNP Lookup

$response = $client->MNPLookup(["number"=> "+number here"]);

// perform number verification

$response = $client->VerifyLookup(["number"=> "+number here"]);

```

### Response handling

The response it's an object of the `CheckMobiResponse` type which exposes the following methods:

| Method        | Description                                                                        |
|---------------|------------------------------------------------------------------------------------|
| is_success    | `boolean` - returns if the response represents an error or not.                    |
| status_code   | `integer` - the HTTP status code received.                                         |
| payload       | `array` or `NULL` - The json decoded response payload as received from the server. |

#### Example

```php

if($response->is_success()) {
    // success 
    print_r($response->payload());
}
else
{
    // failure
    print "error code: ".$response->payload()["code"]." error message: ".$response->payload()["error"];
}
```

[1]:https://checkmobi.com/documentation/api-reference/
