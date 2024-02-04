<?php

namespace checkmobi;

use checkmobi\net\RequestInterface;

class CheckMobiRest
{
    const BASE_URL = "https://api.checkmobi.com";
    const API_VERSION = "v1";

    /**
     * @var RequestInterface
     */
    private $http_client;

    /**
     * CheckMobiRest constructor.
     * @param string $auth_token
     * @param array $options
     * @throws CheckMobiError
     */

    function __construct($auth_token, $options = array())
    {
        if (!is_string($auth_token) || empty($auth_token))
            throw new CheckMobiError("no auth_token specified");

        if(!isset($options["api.base_url"]))
            $options["api.base_url"] = self::BASE_URL;

        if(!isset($options["api.version"]))
            $options["api.version"] = self::API_VERSION;

        if(!isset($options["net.transport"]))
            $options["net.transport"] = RequestInterface::HANDLER_DEFAULT;

        $this->http_client = RequestInterface::Create($auth_token, $options);
    }

    /**
     * @return CheckMobiResponse
     */
    public function GetAccountDetails()
    {
        return $this->http_client->request(RequestInterface::METHOD_GET, '/my-account');
    }

    /**
     * @return CheckMobiResponse
     */
    public function GetCountriesList()
    {
        return $this->http_client->request(RequestInterface::METHOD_GET, '/countries');
    }

    /**
     * @return CheckMobiResponse
     */
    public function GetPrefixes()
    {
        return $this->http_client->request(RequestInterface::METHOD_GET, '/prefixes');
    }

    /**
     * @param array $params
     * @return CheckMobiResponse
     */
    public function CheckNumber($params)
    {
        return $this->http_client->request(RequestInterface::METHOD_POST, '/checknumber', $params);
    }

    /**
     * @param array $params
     * @param string|false $client_ip
     * @return CheckMobiResponse
     */
    public function RequestValidation($params, $client_ip = false)
    {
        return $this->http_client->request(RequestInterface::METHOD_POST, '/validation/request', $params, $client_ip);
    }


    /**
     * @param array $params
     * @return CheckMobiResponse
     */
    public function VerifyLookup($params)
    {
        $number = $this->get_param($params, "number");

        if($number === false)
            return new CheckMobiResponse(0, ["code" => -1, "error" => "Property 'number' not found."]);

        return $this->http_client->request(RequestInterface::METHOD_GET, '/lookup/verify/'.$number);
    }

    /**
     * @param array $params
     * @return CheckMobiResponse
     */
    public function HLRLookup($params)
    {
        $number = $this->get_param($params, "number");

        if($number === false)
            return new CheckMobiResponse(0, ["code" => -1, "error" => "Property 'number' not found."]);

        return $this->http_client->request(RequestInterface::METHOD_GET, '/lookup/hlr/'.$number);
    }

	/**
     * @param array $params
     * @return CheckMobiResponse
     */
    public function MNPLookup($params)
    {
        $number = $this->get_param($params, "number");

        if($number === false)
            return new CheckMobiResponse(0, ["code" => -1, "error" => "Property 'number' not found."]);

        return $this->http_client->request(RequestInterface::METHOD_GET, '/lookup/mnp/'.$number);
    }

    /**
     * @param array $params
     * @return CheckMobiResponse
     */
    public function VerifyPin($params)
    {
        return $this->http_client->request(RequestInterface::METHOD_POST, '/validation/verify', $params);
    }

    /**
     * @param array $params
     * @return CheckMobiResponse
     */
    public function ValidationStatus($params)
    {
        $id = $this->get_param($params, "id");

        if($id === false)
            return new CheckMobiResponse(0, ["code" => -1, "error" => "Property 'id' not found."]);

        return $this->http_client->request(RequestInterface::METHOD_GET, '/validation/status/'.$id);
    }

    /**
     * @param array $params
     * @return CheckMobiResponse
     */

    public function GetRemoteConfigProfile($params)
    {
        return $this->http_client->request(RequestInterface::METHOD_POST, '/validation/remote-config', $params);
    }

    /**
     * @param array $params
     * @param string|false $client_ip
     * @return CheckMobiResponse
     */
    public function SendSMS($params, $client_ip = false)
    {
        return $this->http_client->request(RequestInterface::METHOD_POST, '/sms/send', $params, $client_ip);
    }

    /**
     * @param array $params
     * @return CheckMobiResponse
     */
    public function GetSmsDetails($params)
    {
        $id = $this->get_param($params, "id");

        if($id === false)
            return new CheckMobiResponse(0, ["code" => -1, "error" => "Property 'id' not found."]);

        return $this->http_client->request(RequestInterface::METHOD_GET, '/sms/'.$id);
    }

    /**
     * @param array $params
     * @param string|false $client_ip
     * @return CheckMobiResponse
     */
    public function PlaceCall($params, $client_ip = false)
    {
        return $this->http_client->request(RequestInterface::METHOD_POST, '/call', $params, $client_ip);
    }

    /**
     * @param array $params
     * @return CheckMobiResponse
     */
    public function GetCallDetails($params)
    {
        $id = $this->get_param($params, "id");

        if($id === false)
            return new CheckMobiResponse(0, ["code" => -1, "error" => "Property 'id' not found."]);

        return $this->http_client->request(RequestInterface::METHOD_GET, '/call/'.$id);
    }

    /**
     * @param array $params
     * @return CheckMobiResponse
     */
    public function HangUpCall($params)
    {
        $id = $this->get_param($params, "id");

        if($id === false)
            return new CheckMobiResponse(0, ["code" => -1, "error" => "Property 'id' not found."]);

        return $this->http_client->request(RequestInterface::METHOD_DELETE, '/call/'. $id);
    }

    private function get_param($params, $key)
    {
        if(isset($params[$key]))
            return $params[$key];

        return false;
    }

}
