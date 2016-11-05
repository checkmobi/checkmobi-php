<?php

namespace CheckMobiApi;

if ((@include 'HTTP/Request2.php') == 'OK') 
    define("CHECKMOBI_USE_CURL", FALSE);
else
    define("CHECKMOBI_USE_CURL", TRUE);

class CheckMobiError extends \Exception { }

class CheckMobiRest
{
    private $api;
    private $auth_token;
    private $ch;

    function __construct($auth_token, $url = "https://api.checkmobi.com", $version = "v1") 
    {
        if ((!isset($auth_token)) || (!$auth_token))
            throw new CheckMobiError("no auth_token specified");

        $this->api = $url."/".$version;
        $this->auth_token = $auth_token;
        $this->ch = NULL;
    }

    private function curl_request($method, $path, $params) 
    {
        $url = $this->api.$path;

        $this->ch = @curl_init();

        if (curl_errno($this->ch))
            return array("status" => 0, "response" => array("error" => curl_error($this->ch)));

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => "CheckMobi/Curl",
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_HEADER => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_VERBOSE => FALSE,
            CURLOPT_SSL_VERIFYPEER => FALSE);

        $headers = array('Authorization: '.$this->auth_token, 'Connection: close');

        if ($method === "POST") 
        {
            $json_params = json_encode($params);
            $options[CURLOPT_POSTFIELDS] = $json_params;
            $options[CURLOPT_POST] = TRUE;

            array_push($headers, "Content-Type: application/json");
            array_push($headers, 'Content-Length: '.strlen($json_params));
        }

        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($this->ch, $options);
        $res = @curl_exec($this->ch);

        $status = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        if ($res === FALSE) 
        {
            $err = curl_error($this->ch);
            @curl_close($this->ch);
            return array("status" => $status, "response" => array("error" => $err));
        }

        @curl_close($this->ch);

        $result = json_decode($res, TRUE);
        return array("status" => $status, "response" => $result);
    }

    private function http2_request($method, $path, $params) 
    {
        $url = $this->api.$path;

        $http_method = \HTTP_Request2::METHOD_POST;

        if (!strcmp($method, "GET")) 
            $http_method = \HTTP_Request2::METHOD_GET;
        else if (!strcmp($method, "DELETE")) 
            $http_method = \HTTP_Request2::METHOD_DELETE;

        $req = new \HTTP_Request2($url, $http_method);

        if ($http_method === \HTTP_Request2::METHOD_POST && $params)
            $req->setBody(json_encode($params));

        $req->setAdapter('curl');
        $req->setConfig(array('timeout' => 30, 'ssl_verify_peer' => FALSE));

        $req->setHeader(array('Authorization' => $this->auth_token, 
                              'Connection' => 'close', 
                              'User-Agent' => 'CheckMobi/http2_request', 
                              'Content-type' => 'application/json'));

        $r = $req->send();
        $status = $r->getStatus();
        $body = $r->getbody();
        $response = json_decode($body, true);
        return array("status" => $status, "response" => $response);
    }

    private function request($method, $path, $params=array()) 
    {
        if (CHECKMOBI_USE_CURL === TRUE)
            return $this->curl_request($method, $path, $params);

        return $this->http2_request($method, $path, $params);
    }

    private function pop($params, $key) 
    {
        $val = $params[$key];

        if (!$val)
            throw new CheckMobiError($key." parameter not found");

        unset($params[$key]);
        return $val;
    }

    public function GetAccountDetails()
    {
        return $this->request('GET', '/my-account', FALSE);
    }

    public function GetCountriesList()
    {
        return $this->request('GET', '/countries', FALSE);
    }

    public function GetPrefixes()
    {
        return $this->request('GET', '/prefixes', FALSE);
    }

    public function CheckNumber($params)
    {
        return $this->request('POST', '/checknumber', $params);
    }

    public function RequestValidation($params)
    {
        return $this->request('POST', '/validation/request', $params);
    }

    public function VerifyPin($params)
    {
        return $this->request('POST', '/validation/verify', $params);
    }

    public function ValidationStatus($params)
    {
        $id = $this->pop($params, "id");
        return $this->request('GET', '/validation/status/'.$id, FALSE);
    }

    public function SendSMS($params)
    {
        return $this->request('POST', '/sms/send', $params);
    }

    public function GetSmsDetails($params)
    {
        $id = $this->pop($params, "id");
        return $this->request('GET', '/sms/'.$id, FALSE);
    }

    public function PlaceCall($params)
    {
        return $this->request('POST', '/call', $params);
    }

    public function GetCallDetails($params)
    {
        $id = $this->pop($params, "id");
        return $this->request('GET', '/call/'.$id, FALSE);
    }

    public function HangUpCall($params)
    {
        $id = $this->pop($params, "id");
        return $this->request('DELETE', '/call/'. $id, FALSE);
    }

}

?>
