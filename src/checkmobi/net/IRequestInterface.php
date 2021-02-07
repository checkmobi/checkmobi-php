<?php
namespace checkmobi\net;

use checkmobi\CheckMobiError;
use checkmobi\CheckMobiResponse;

interface IRequestInterface
{
    /**
     * @param string $method
     * @param string $path
     * @param false|array $params
     * @return CheckMobiResponse
     */
    public function request($method, $path, $params = FALSE);

    /**
     * @return boolean
     */
    public static function IsAvailable();

    /**
     * @param string $auth_token
     * @param array $options
     * @return RequestInterface
     * @throws CheckMobiError
     */
    public static function Create($auth_token, $options);
}
