<?php
/**
 * Created by PhpStorm.
 * User: alkan
 * Date: 5/20/15
 * Time: 2:32 PM
 */



class Site_Client {
    private $url;
    private $postString;
    private $httpResponse;
    private $ch;
    private $headers;
    private $errNo;

    public function __construct($url)
    {
        $this->url = $url;
        $this->errNo = null;
        $this->ch = curl_init($this->url);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch,CURLOPT_TIMEOUT,1000);

        $this->headers = array();

    }

    public function __destruct()
    {
        curl_close($this->ch);
    }

    public function setPostData($paramStr)
    {
        $this->postString = $paramStr;
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->postString);
    }

    public function send()
    {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
        $this->httpResponse = curl_exec($this->ch);
        $this->errNo = curl_errno($this->ch);
    }

    public function getHttpResponse()
    {
        $this->errNo = null;
        return $this->httpResponse;
    }

    public function isSuccess()
    {
        return !$this->errNo;
    }

    public function getErrorMessage()
    {
        if($this->errNo)
        {
            return curl_strerror($this->errNo);
        }

        return null;
    }

    public function setMethod($method)
    {
        $method = strtoupper($method);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
    }

    public function setHeader($header=array())
    {
        $this->headers = array_merge($header,$this->headers);
    }

}

