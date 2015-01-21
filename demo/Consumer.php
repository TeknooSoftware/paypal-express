<?php

namespace Acme\Demo;

use UniAlteri\Paypal\Express\Entity\ConsumerInterface;

/**
 * Class Consumer
 * Demo business class to represent a consumer
 * @package Acme\Demo
 */
class Consumer implements ConsumerInterface
{
    /**
     * Return the consumer name from the vendor'sinformation system
     * @return string
     */
    public function getConsumerName()
    {
        return 'Richard Deloge';
    }

    /**
     * Return the consumer address from the vendor'sinformation system
     * @return string
     */
    public function getShippingAddress()
    {
        return '1 rue de Bruxelles';
    }

    /**
     * Return the consumer extra address from the vendor'sinformation system
     * to allow consumer to input additional inforlations
     * @return string
     */
    public function getShippingExtraAddress()
    {
        return 'Le Colisee';
    }

    /**
     * Return the consumer zip from the vendor'sinformation system
     * @return string
     */
    public function getShippingZip()
    {
        return 14120;
    }

    /**
     * Return the consumer city from the vendor'sinformation system
     * @return string
     */
    public function getShippingCity()
    {
        return 'Mondeville';
    }

    /**
     * Return the consumer state from the vendor'sinformation system
     * (for United State only)
     * @return string
     */
    public function getShippingState()
    {
        return 'Normandy';
    }

    /**
     * Return the consumer country code from the vendor'sinformation system
     * @return string
     */
    public function getShippingCountryCode()
    {
        return 'France';
    }

    /**     *
     * Return the consumer phone from the vendor'sinformation system
     * @return string
     */
    public function getPhone()
    {
        return '0033123456789';
    }
}