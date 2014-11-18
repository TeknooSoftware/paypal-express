<?php

namespace UniAlteri\Paypal\Express\Transport;

use Zeroem\CurlBundle\Curl\RequestGenerator;

class Curl93 implements TransportInterface
{
    /**
     * User id in paypal
     * @var string
     */
    private $userId;

    /**
     * Password in paypal
     * @var string
     */
    private $password;

    /**
     * Signature in paypal
     * @var string
     */
    private $signature;

    /**
     * API End point to communicate with paypal
     * @var string
     */
    protected $apiEndPoint;

    /**
     * URL to redirect the user
     * @var string
     */
    protected $paypalUrl;

    /**
     * Paypal API Version used
     * @var int
     */
    protected $paypalVersion = 93;

    /**
     * BN Code 	is only applicable for partners
     * @var string
     */
    protected $bNCode = 'PP-ECWizard';

    /**
     * Curl Timeout
     * @var int
     */
    protected $apiTimeout = 60;

    /**
     * @var RequestGenerator
     */
    protected $requestGenerator;

    /**
     * Setter to define the user identifier to use with the api to get an access
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Return the user identifier to use with the api to get an access
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Setter to define the password to use with the api to get an access
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Return the password to use with the api to get an access
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Setter to define the user's signature to usee with the api to get an access
     * @param string $signature
     * @return $this
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Return the user's signature to usee with the api to get an access
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Setter to define the API End point to use for next transactions
     * @param string $apiEndPoint
     * @return $this
     */
    public function setApiEndPoint($apiEndPoint)
    {
        $this->apiEndPoint = $apiEndPoint;

        return $this;
    }

    /**
     * Getter the get the API End point to use for next transactions
     * @return string
     */
    public function getApiEndPoint()
    {
        return $this->apiEndPoint;
    }

    /**
     * Setter to define the url to contact the Paypal api
     * @param string $url
     * @return $this
     */
    public function setPaypalUrl($url)
    {
        $this->paypalUrl = $url;

        return $this;
    }

    /**
     * Return the url used to contact the Paypal api
     * @return string
     */
    public function getPaypalUrl()
    {
        return $this->paypalUrl;
    }

    /**
     * Return the current version of the api managed by this transport
     * @return string
     */
    public function getPaypalApiVersion()
    {
        return $this->paypalVersion;
    }

    /**
     * Setter to define the timeout accepted by the transport to close the transaction when the api
     * could not respond
     * @param int $second
     * @return $this
     */
    public function setApiTimeout($second)
    {
        $this->apiTimeout = $second;

        return $this;
    }

    /**
     * Return the max time accepted by the transport to get an answer of the api, in seconds
     * @return int
     */
    public function getApiTimeout()
    {
        return $this->apiTimeout;
    }
}