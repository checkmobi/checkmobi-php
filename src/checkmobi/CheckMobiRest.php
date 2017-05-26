<?php

namespace checkmobi;

use checkmobi\net\RequestInterface;

class CheckMobiRest
{
    const BASE_URL = "https://api.checkmobi.com";
    const API_VERSION = "v1";

    private $http_client;

    function __construct($auth_token, $engine = RequestInterface::HANDLER_UNKNOWN, $base_url = self::BASE_URL, $version = self::API_VERSION)
    {
        if ((!isset($auth_token)) || (!$auth_token))
            throw new CheckMobiError("no auth_token specified");

        $url = $base_url."/".$version;
        $this->http_client = RequestInterface::Create($url, $auth_token, $engine);
    }

    public function GetAccountDetails()
    {
        return $this->http_client->request(RequestInterface::METHOD_GET, '/my-account', FALSE);
    }

    public function GetCountriesList()
    {
        return $this->http_client->request(RequestInterface::METHOD_GET, '/countries', FALSE);
    }

    public function GetPrefixes()
    {
        return $this->http_client->request(RequestInterface::METHOD_GET, '/prefixes', FALSE);
    }

    public function CheckNumber($params)
    {
        return $this->http_client->request(RequestInterface::METHOD_POST, '/checknumber', $params);
    }

    public function RequestValidation($params)
    {
        return $this->http_client->request(RequestInterface::METHOD_POST, '/validation/request', $params);
    }

    public function VerifyPin($params)
    {
        return $this->http_client->request(RequestInterface::METHOD_POST, '/validation/verify', $params);
    }

    public function ValidationStatus($params)
    {
        $id = $this->pop($params, "id");
        return $this->http_client->request(RequestInterface::METHOD_GET, '/validation/status/'.$id, FALSE);
    }

    public function SendSMS($params)
    {
        return $this->http_client->request(RequestInterface::METHOD_POST, '/sms/send', $params);
    }

    public function GetSmsDetails($params)
    {
        $id = $this->pop($params, "id");
        return $this->http_client->request(RequestInterface::METHOD_GET, '/sms/'.$id, FALSE);
    }

    public function PlaceCall($params)
    {
        return $this->http_client->request(RequestInterface::METHOD_POST, '/call', $params);
    }

    public function GetCallDetails($params)
    {
        $id = $this->pop($params, "id");
        return $this->http_client->request(RequestInterface::METHOD_GET, '/call/'.$id, FALSE);
    }

    public function HangUpCall($params)
    {
        $id = $this->pop($params, "id");
        return $this->http_client->request(RequestInterface::METHOD_DELETE, '/call/'. $id, FALSE);
    }

    private function pop($params, $key)
    {
        $val = $params[$key];

        if (!$val)
            throw new CheckMobiError($key." parameter not found");

        unset($params[$key]);
        return $val;
    }

}
