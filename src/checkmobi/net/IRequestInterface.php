<?php
namespace checkmobi\net;

interface IRequestInterface
{
    public function request($method, $path, $params = FALSE);

    public static function IsAvailable();

    public static function Create($base_url, $auth_token, $engine);
}
