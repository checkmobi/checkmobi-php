<?php

namespace checkmobi\net;

@include_once 'HTTP/Request2.php';

use checkmobi\CheckMobiResponse;
use HTTP_Request2;
use HTTP_Request2_Exception;

class HttpRequest2Handler extends RequestInterface
{
    const USER_AGENT = "checkmobi/php-http2_request";

    private $has_curl;
    private $config;

    public function __construct($base_url, $auth_token, $has_curl, $options)
    {
        parent::__construct($base_url, $auth_token, $options);

        $this->has_curl = $has_curl;
        $this->config = array(
            'timeout' => $this->timeout_sec,
            'connect_timeout' => $this->timeout_sec,
            'ssl_verify_peer' => $this->ssl_verify_peer
        );
    }

    public static function IsAvailable()
    {
        return class_exists("HTTP_Request2");
    }

    public function request($method, $path, $params = false, $client_ip = false)
    {
        try
        {
            $http_method = self::GetMethod($method);

            if($http_method === false)
                return new CheckMobiResponse(0, ["code" => -1, "error" => "Unsupported HTTP method: '".$method."'"]);

            $req = new HTTP_Request2($this->GetUrl($path), $http_method);

            if ($http_method === HTTP_Request2::METHOD_POST && is_array($params))
                $req->setBody(json_encode($params));

            if($this->has_curl)
                $req->setAdapter('curl');

            $req->setConfig($this->config);

            $headers = array(
                'Authorization' => $this->auth_token,
                'Connection' => 'close',
                'User-Agent' => self::USER_AGENT,
                'Content-type' => 'application/json');

            if($client_ip !== false)
                $headers[] = "X-Client-IP: " . $client_ip;

            $req->setHeader($headers);
            $r = $req->send();
            return new CheckMobiResponse($r->getStatus(), json_decode($r->getbody(), true));
        }
        catch (HTTP_Request2_Exception $ex)
        {
            return new CheckMobiResponse(0, ["code" => -1, "error" => $ex->getMessage()]);
        }
    }

    private static function GetMethod($method)
    {
        if ($method  == RequestInterface::METHOD_POST)
            return HTTP_Request2::METHOD_POST;
        else if ($method == RequestInterface::METHOD_GET)
            return HTTP_Request2::METHOD_GET;
        else if ($method  == RequestInterface::METHOD_DELETE)
            return HTTP_Request2::METHOD_DELETE;

        return false;
    }
}
