<?php

namespace checkmobi\net;

use checkmobi\CheckMobiError;
use checkmobi\CheckMobiResponse;

class CurlHandler extends RequestInterface
{
    const USER_AGENT = "checkmobi/curl";

    private $ch;

    function __construct($base_url, $auth_token, $options)
    {
        parent::__construct($base_url, $auth_token, $options);

        $this->ch = curl_init();

        if($this->ch === FALSE)
             throw new CheckMobiError("CURL is not available");
    }

    function __destruct()
    {
        if($this->ch !== FALSE)
            curl_close($this->ch);
    }

    public static function IsAvailable()
    {
        return function_exists('curl_version');
    }

    public function request($method, $path, $params = FALSE)
    {
        if (curl_errno($this->ch))
            return new CheckMobiResponse(0, ["code" => -1, "error" => curl_error($this->ch)]);

        curl_reset($this->ch);

        $options = array(
            CURLOPT_URL => $this->GetUrl($path),
            CURLOPT_USERAGENT => self::USER_AGENT,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_DEFAULT,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_TIMEOUT => $this->timeout_sec,
            CURLOPT_CONNECTTIMEOUT => $this->timeout_sec,
            CURLOPT_HEADER => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_VERBOSE => FALSE,
            CURLOPT_SSL_VERIFYPEER => $this->ssl_verify_peer);

        $headers = array('Authorization: '.$this->auth_token);

        if ($method === RequestInterface::METHOD_POST)
        {
            $options[CURLOPT_POST] = TRUE;

            if(is_array($params))
            {
                $json_params = json_encode($params);
                $options[CURLOPT_POSTFIELDS] = $json_params;
                array_push($headers, "Content-Type: application/json");
                array_push($headers, 'Content-Length: '.strlen($json_params));
            }
        }

        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($this->ch, $options);
        $res = curl_exec($this->ch);

        if ($res === FALSE)
            return new CheckMobiResponse(0, ["code" => -1, "error" => curl_error($this->ch)]);

        $status = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        return new CheckMobiResponse($status, json_decode($res, TRUE));
    }

}