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

    const HANDLER_DEFAULT = 0;
    const HANDLER_CURL = 1;
    const HANDLER_HTTP2 = 2;

    protected $base_url;
    protected $auth_token;
    protected $timeout_sec = 30;
    protected $ssl_verify_peer = true;

    function __construct($base_url, $auth_token, $options = array())
    {
        $this->base_url = $base_url;
        $this->auth_token = $auth_token;
        $this->apply_options($options);
    }

    protected function GetUrl($path)
    {
        return $this->base_url.$path;
    }

    public static function Create($auth_token, $options)
    {
        $base_url = $options["api.base_url"]."/".$options["api.version"];
        $has_curl = CurlHandler::IsAvailable();

        switch ($options["net.transport"])
        {
            case self::HANDLER_DEFAULT:
                if($has_curl)
                    return new CurlHandler ($base_url, $auth_token, $options);

                if(HttpRequest2Handler::IsAvailable())
                    return new HttpRequest2Handler ($base_url, $auth_token, false, $options);
                break;

            case self::HANDLER_CURL:
                if($has_curl)
                    return new CurlHandler ($base_url, $auth_token, $options);
                break;

            case self::HANDLER_HTTP2:
                if(HttpRequest2Handler::IsAvailable())
                    return new HttpRequest2Handler ($base_url, $auth_token, $has_curl, $options);
                break;
        }

        throw new CheckMobiError("Make sure CURL or HTTP_Request2 is available");
    }

    private function apply_options($opt)
    {
        if(isset($opt["net.timeout"]))
            $this->timeout_sec = $opt["net.timeout"];

        if(isset($opt["net.ssl_verify_peer"]))
            $this->ssl_verify_peer = $opt["net.ssl_verify_peer"];
    }

}
