<?php

namespace checkmobi\net;

if ((@include 'HTTP/Request2.php') == 'OK') 
    define("HAS_HTTP_REQUEST2", TRUE);
else
    define("HAS_HTTP_REQUEST2", FALSE);

use checkmobi\CheckMobiError;
use \HTTP_Request2;

class HttpRequest2Handler extends RequestInterface
{
    const USER_AGENT = "checkmobi/http2_request";
    
    private $has_curl;
    private $config;
    
    public function __construct($base_url, $auth_token, $curl)
    {
        parent::__construct($base_url, $auth_token);
        
        $this->has_curl = $curl;
        $this->config = array(
            'timeout' => $this->timeout_sec, 
            'connect_timeout' => $this->timeout_sec,
            'ssl_verify_peer' => $this->ssl_verify_peer);
    }
    
    public function request($method, $path, $params)
    {
        $http_method = self::GetMethod($method);

        $req = new HTTP_Request2($this->GetUrl($path), $http_method);

        if ($http_method === HTTP_Request2::METHOD_POST && is_array($params))
            $req->setBody(json_encode($params));

        if($this->has_curl)
            $req->setAdapter('curl');
        
        $req->setConfig($this->config);

        $req->setHeader(array('Authorization' => $this->auth_token, 
                              'Connection' => 'close', 
                              'User-Agent' => self::USER_AGENT, 
                              'Content-type' => 'application/json'));

        $r = $req->send();
        $status = $r->getStatus();
        $body = $r->getbody();
        $response = json_decode($body, true);
        return array("status" => $status, "response" => $response);
    }
    
    private static function GetMethod($method)
    {
        if ($method  == RequestInterface::METHOD_POST)
            return HTTP_Request2::METHOD_POST;
        else if ($method == RequestInterface::METHOD_GET) 
            return HTTP_Request2::METHOD_GET;
        else if ($method  == RequestInterface::METHOD_DELETE)
            return HTTP_Request2::METHOD_DELETE;
        
        throw new CheckMobiError("Unavailable method: ".$method);
    }

    public static function IsAvailable()
    {
        return HAS_HTTP_REQUEST2;
    }

}

