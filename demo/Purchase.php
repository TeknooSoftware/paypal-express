<?php

namespace Acme\Demo;

use UniAlteri\Paypal\Express\Entity\ConsumerInterface;
use UniAlteri\Paypal\Express\Entity\PurchaseInterface;

/**
 * Class Purchase
 * Demo business class representing a purchase
 * @package Acme\Demo
 */
class Purchase implements PurchaseInterface
{
    /**
     * @var string
     */
    protected $baseUrl = null;

    /**
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get the amount of the purchase, feet included, in float representation
     * @return float
     */
    public function getAmount()
    {
        return 314.15;
    }

    /**
     * Get the payment action to use in the transaction (sale, ..)
     * @return string
     */
    public function getPaymentAction()
    {
        return 'SALE';
    }

    /**
     * Get the url to redirect the consumer after the payment operation
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->baseUrl.'?method=return';
    }

    /**
     * Get the url to redirect the consumer when it cancel the transaction in paypal
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->baseUrl.'?method=cancel';
    }

    /**
     * Get the currency used for this transaction
     * @return string
     */
    public function getCurrencyCode()
    {
        return 'EUR';
    }

    /**
     * Get the consumer of this transaction
     * @return ConsumerInterface
     */
    public function getConsumer()
    {
        return new Consumer();
    }
}