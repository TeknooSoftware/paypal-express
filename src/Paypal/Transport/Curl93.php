<?php
/**
 * Paypal Express.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/paypal Project website
 *
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     0.8.2
 */

namespace UniAlteri\Paypal\Express\Transport;

use UniAlteri\Curl\RequestGenerator;

/**
 * Class Curl93
 * Implementation of TransportInterface with Curl library to use the paypal api.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/paypal Project website
 *
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Curl93 implements TransportInterface
{
    /**
     * User id in paypal.
     *
     * @var string
     */
    private $userId;

    /**
     * Password in paypal.
     *
     * @var string
     */
    private $password;

    /**
     * Signature in paypal.
     *
     * @var string
     */
    private $signature;

    /**
     * API End point to communicate with paypal.
     *
     * @var string
     */
    protected $apiEndPoint;

    /**
     * URL to redirect the user.
     *
     * @var string
     */
    protected $paypalUrl;

    /**
     * Paypal API Version used.
     *
     * @var int
     */
    protected $paypalVersion = 93;

    /**
     * BN Code 	is only applicable for partners.
     *
     * @var string
     */
    protected $bNCode = 'PP-ECWizard';

    /**
     * Curl Timeout.
     *
     * @var int
     */
    protected $apiTimeout = 60;

    /**
     * @var RequestGenerator
     */
    protected $requestGenerator;

    /**
     * @param string|null           $userId
     * @param string|null           $password
     * @param string|null           $signature
     * @param string|null           $apiEndPoint
     * @param string|null           $paypalUrl
     * @param int|null              $paypalVersion
     * @param string|null           $bNCode
     * @param int|null              $apiTimeout
     * @param RequestGenerator|null $requestGenerator
     */
    public function __construct(
        $userId = null,
        $password = null,
        $signature = null,
        $apiEndPoint = null,
        $paypalUrl = null,
        $paypalVersion = 93,
        $bNCode = 'PP-ECWizard',
        $apiTimeout = 60,
        $requestGenerator = null
    ) {
        $this->userId = $userId;
        $this->password = $password;
        $this->signature = $signature;
        $this->apiEndPoint = $apiEndPoint;
        $this->paypalUrl = $paypalUrl;
        $this->paypalVersion = $paypalVersion;
        $this->bNCode = $bNCode;
        $this->apiTimeout = $apiTimeout;
        $this->requestGenerator = $requestGenerator;
    }

    /**
     * Setter to define the user identifier to use with the api to get an access.
     *
     * @param string $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Return the user identifier to use with the api to get an access.
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Setter to define the password to use with the api to get an access.
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Return the password to use with the api to get an access.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Setter to define the user's signature to usee with the api to get an access.
     *
     * @param string $signature
     *
     * @return $this
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Return the user's signature to usee with the api to get an access.
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Setter to define the API End point to use for next transactions.
     *
     * @param string $apiEndPoint
     *
     * @return $this
     */
    public function setApiEndPoint($apiEndPoint)
    {
        $this->apiEndPoint = $apiEndPoint;

        return $this;
    }

    /**
     * Getter the get the API End point to use for next transactions.
     *
     * @return string
     */
    public function getApiEndPoint()
    {
        return $this->apiEndPoint;
    }

    /**
     * Setter to define the url to contact the Paypal api.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setPaypalUrl($url)
    {
        $this->paypalUrl = $url;

        return $this;
    }

    /**
     * Return the url used to contact the Paypal api.
     *
     * @return string
     */
    public function getPaypalUrl()
    {
        return $this->paypalUrl;
    }

    /**
     * Return the current version of the api managed by this transport.
     *
     * @return string
     */
    public function getPaypalApiVersion()
    {
        return $this->paypalVersion;
    }

    /**
     * Setter to define the timeout accepted by the transport to close the transaction when the api
     * could not respond.
     *
     * @param int $second
     *
     * @return $this
     */
    public function setApiTimeout($second)
    {
        $this->apiTimeout = $second;

        return $this;
    }

    /**
     * Return the max time accepted by the transport to get an answer of the api, in seconds.
     *
     * @return int
     */
    public function getApiTimeout()
    {
        return $this->apiTimeout;
    }

    /**
     * @param string               $methodName
     * @param ArgumentBagInterface $arguments
     *
     * @return \ArrayAccess
     */
    public function call($methodName, $arguments)
    {
        $request = $this->requestGenerator->getRequest();

        //setting the curl parameters.
        $request->setMethod('POST');
        $request->setOption(CURLOPT_URL, $this->apiEndPoint);
        $request->setOption(CURLOPT_VERBOSE, true);

        //turning off the server and peer verification(TrustManager Concept).
        $request->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $request->setOption(CURLOPT_SSL_VERIFYHOST, 0);

        $request->setOption(CURLOPT_RETURNTRANSFER, true);
        $request->setOption(CURLOPT_POST, true);
        $request->setOption(CURLOPT_TIMEOUT, $this->apiTimeout*10);
        $request->setOption(CURLOPT_CONNECTTIMEOUT, $this->apiTimeout);

        $argumentsArray = [];
        foreach ($arguments->toArray() as $key => $value) {
            $argumentsArray[$key] = $value;
        }

        //Finalise request with arguments
        $argumentsArray['METHOD'] = $methodName;
        $argumentsArray['VERSION'] = $this->paypalVersion;
        $argumentsArray['PWD'] = $this->password;
        $argumentsArray['USER'] = $this->userId;
        $argumentsArray['SIGNATURE'] = $this->signature;
        $argumentsArray['BUTTONSOURCE'] = $this->bNCode;

        //setting the nvpreq as POST FIELD to curl
        $request->setOption(CURLOPT_POSTFIELDS, http_build_query($argumentsArray));

        //getting response from server
        $response = $request->execute();

        //converting request response to an Associative Array
        $resultArray = array();
        parse_str(urldecode($response), $resultArray);

        return new \ArrayObject($resultArray);
    }
}
