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
     * Return raw value from the request
     * @return mixed
     */
    public function getRawValues();
}