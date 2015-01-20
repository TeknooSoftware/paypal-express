<?php

namespace UniAlteri\Paypal\Express\Service;

/**
 * Class TransactionResult
 * Class to manipulate result
 * @package UniAlteri\Paypal\Express\Service
 */
class TransactionResult implements TransactionResultInterface
{
    /**
     * Return of the api
     * @var array
     */
    protected $values;

    /**
     * Constructor to initialize the result object
     * @param array|\ArrayAccess $values
     */
    public function __construct($values)
    {
        $this->values = $values;
    }

    /**
     * Return the raw value of the ACK field from the paypal API for this transaction
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
     * Return a boolean to test if the operation via the api is successful
     * @return boolean
     */
    public function isSuccessful()
    {
        $ack = strtoupper($this->getAckValue());
        if ('SUCCESS' == $ack || 'SUCCESSWITHWARNING' == $ack) {
            return true;
        }

        return false;
    }

    /**
     * Return the raw value of the Token field from the paypal API for this transaction
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
     * Return the raw value of the PayerId field from the paypal API for this transaction
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
     * Return raw value from the request
     * @return mixed
     */
    public function getRawValues()
    {
        return $this->values;
    }
}