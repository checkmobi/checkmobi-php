<?php

namespace checkmobi;

class CheckMobiResponse
{
    private $status_code;
    private $payload;

    function __construct($status_code, $payload)
    {
        $this->status_code = $status_code;
        $this->payload = $payload;
    }

    public function is_success()
    {
        return $this->status_code == 200 || $this->status_code == 204;
    }

    public function status_code()
    {
        return $this->status_code;
    }

    public function payload()
    {
        return $this->payload;
    }
}