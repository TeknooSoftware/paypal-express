<?php

namespace UniAlteri\Paypal\Express\Service;

interface TransactionResultInterface
{
    /**
     * Return the raw value of the ACK field from the paypal API for this transaction
     * @return string
     */
    public function getAckValue();
}