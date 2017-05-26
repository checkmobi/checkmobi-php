<?php

namespace checkmobi\net;

use checkmobi\CheckMobiError;

abstract class RequestInterface implements IRequestInterface
{
    //methods

    const METHOD_POST = "POST";
    const METHOD_GET = "GET";
    const METHOD_DELETE = "DELETE";

    //engines

    const HANDLER_UNKNOWN = 0;
    const HANDLER_CURL = 1;
    const HANDLER_HTTP2 = 2;

    protected $base_url;
    protected $auth_token;
    protected $timeout_sec = 30;
    protected $ssl_verify_peer = FALSE;

    function __construct($base_url, $auth_token)
    {
        $this->base_url = $base_url;
        $this->auth_token = $auth_token;
    }

    protected function GetUrl($path)
    {
        return $this->base_url.$path;
    }

    public static function Create($base_url, $auth_token, $engine = self::HANDLER_UNKNOWN)
    {
        $has_curl = CurlHandler::IsAvailable();

        switch ($engine)
        {
            case self::HANDLER_UNKNOWN:
                if($has_curl)
                    return new CurlHandler ($base_url, $auth_token);

                if(HttpRequest2Handler::IsAvailable())
                    return new HttpRequest2Handler ($base_url, $auth_token, false);
                break;

            case self::HANDLER_CURL:
                if($has_curl)
                    return new CurlHandler ($base_url, $auth_token);
                break;

            case self::HANDLER_HTTP2:
                if(HttpRequest2Handler::IsAvailable())
                    return new HttpRequest2Handler ($base_url, $auth_token, $has_curl);
                break;
        }

        throw new CheckMobiError("Make sure CURL or HTTP_Request2 is available");
    }

}
