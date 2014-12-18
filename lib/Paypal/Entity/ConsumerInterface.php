<?php

namespace UniAlteri\Paypal\Express\Entity;

/**
 * Interface ConsumerInterface
 * Interface to represent consumer accounts of transactions to get informations
 * for Paypal API
 * @package UniAlteri\Paypal\Express\Entity
 */
interface ConsumerInterface
{
    /**
     * Return the consumer name from the vendor'sinformation system
     * @return string
     */
    public function getConsumerName();

    /**
     * Return the consumer address from the vendor'sinformation system
     * @return string
     */
    public function getShippingAddress();

    /**
     * Return the consumer extra address from the vendor'sinformation system
     * to allow consumer to input additional inforlations
     * @return string
     */
    public function getShippingExtraAddress();

    /**
     * Return the consumer zip from the vendor'sinformation system
     * @return string
     */
    public function getShippingZip();

    /**
     * Return the consumer city from the vendor'sinformation system
     * @return string
     */
    public function getShippingCity();

    /**
     * Return the consumer state from the vendor'sinformation system
     * (for United State only)
     * @return string
     */
    public function getShippingState();

    /**
     * Return the consumer country code from the vendor'sinformation system
     * @return string
     */
    public function getShippingCountryCode();

    /**     *
     * Return the consumer phone from the vendor'sinformation system
     * @return string
     */
    public function getPhone();
}