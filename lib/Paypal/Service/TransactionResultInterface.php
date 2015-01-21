<?php

namespace UniAlteri\Paypal\Express\Service;

/**
 * Interface TransactionResultInterface
 * Interface to define object parsing paypal return
 * @package UniAlteri\Paypal\Express\Service
 */
interface TransactionResultInterface
{
    /**
     * Return the raw value of the ACK field from the paypal API for this transaction
     * @return string
     */
    public function getAckValue();

    /**
     * Return a boolean to test if the operation via the api is successful
     * @return boolean
     */
    public function isSuccessful();

    /**
     * Return the raw value of the Token field from the paypal API for this transaction
     * @return string
     */
    public function getTokenValue();

    /**
     * Return the raw value of the PayerId field from the paypal API for this transaction
     * @return string
     */
    public function getPayerIdValue();

    /**
     * Return the raw value of the Timestamp field from the paypal API for this transaction
     * @return string
     */
    public function getTimestampValue();

    /**
     * Return the raw value of the CorrelationId field from the paypal API for this transaction
     * @return string
     */
    public function getCorrelationIdValue();

    /**
     * Return the raw value of the Version field from the paypal API for this transaction
     * @return string
     */
    public function getVersionValue();

    /**
     * Return the raw value of the Build field from the paypal API for this transaction
     * @return string
     */
    public function getBuildValue();

    /**
     * Return errors from paypal
     * @return ErrorInterface[]
     */
    public function getErrors();

    /**
     * Return raw value from the request
     * @return mixed
     */
    public function getRawValues();
}