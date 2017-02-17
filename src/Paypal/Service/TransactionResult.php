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
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @version     0.8.3
 */

namespace Teknoo\Paypal\Express\Service;

/**
 * Class TransactionResult
 * Class to manipulate result.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class TransactionResult implements TransactionResultInterface
{
    /**
     * Return of the api.
     *
     * @var array
     */
    protected $values;

    /**
     * Constructor to initialize the result object.
     *
     * @param array|\ArrayAccess $values
     */
    public function __construct($values)
    {
        $this->values = $values;
    }

    /**
     * Return the raw value of the ACK field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getAckValue()
    {
        if (isset($this->values['ACK'])) {
            return $this->values['ACK'];
        }

        throw new \RuntimeException('Error, the ACK value is not available in the response');
    }

    /**
     * Return a boolean to test if the operation via the api is successful.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        $ack = \strtoupper($this->getAckValue());
        if ('SUCCESS' == $ack || 'SUCCESSWITHWARNING' == $ack) {
            return true;
        }

        return false;
    }

    /**
     * Return the raw value of the Token field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getTokenValue()
    {
        if (isset($this->values['TOKEN'])) {
            return $this->values['TOKEN'];
        }

        throw new \RuntimeException('Error, the TOCKEN value is not available in the response');
    }

    /**
     * Return the raw value of the PayerId field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getPayerIdValue()
    {
        if (isset($this->values['PAYERID'])) {
            return $this->values['PAYERID'];
        }

        throw new \RuntimeException('Error, the PAYERID value is not available in the response');
    }

    /**
     * Return the raw value of the Timestamp field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getTimestampValue()
    {
        if (isset($this->values['TIMESTAMP'])) {
            return $this->values['TIMESTAMP'];
        }

        throw new \RuntimeException('Error, the TIMESTAMP value is not available in the response');
    }

    /**
     * Return the raw value of the CorrelationId field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getCorrelationIdValue()
    {
        if (isset($this->values['CORRELATIONID'])) {
            return $this->values['CORRELATIONID'];
        }

        throw new \RuntimeException('Error, the CORRELATIONID value is not available in the response');
    }

    /**
     * Return the raw value of the Version field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getVersionValue()
    {
        if (isset($this->values['VERSION'])) {
            return $this->values['VERSION'];
        }

        throw new \RuntimeException('Error, the VERSION value is not available in the response');
    }

    /**
     * Return the raw value of the Build field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getBuildValue()
    {
        if (isset($this->values['BUILD'])) {
            return $this->values['BUILD'];
        }

        throw new \RuntimeException('Error, the BUILD value is not available in the response');
    }

    /**
     * Return errors from paypal.
     *
     * @return ErrorInterface[]
     */
    public function getErrors()
    {
        $errorList = [];

        $lineIndex = 0;
        while (isset($this->values['L_ERRORCODE'.$lineIndex])) {
            $errorList[] = new Error(
                $this->values['L_ERRORCODE'.$lineIndex],
                $this->values['L_SHORTMESSAGE'.$lineIndex],
                $this->values['L_LONGMESSAGE'.$lineIndex],
                $this->values['L_SEVERITYCODE'.$lineIndex]
            );

            ++$lineIndex;
        }

        return $errorList;
    }

    /**
     * Return raw value from the request.
     *
     * @return mixed
     */
    public function getRawValues()
    {
        return $this->values;
    }
}
