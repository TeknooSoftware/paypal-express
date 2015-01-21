<?php

namespace UniAlteri\Paypal\Express\Service;

/**
 * Interface ErrorInterface
 * Interface to define errors returned by paypal
 * @package UniAlteri\Paypal\Express\Service
 */
interface ErrorInterface
{
    /**
     * Get the code of the error
     * @return string
     */
    public function getCode();

    /**
     * Return the short message of the error
     * @return string
     */
    public function getShortMessage();

    /**
     * Return the long message of the error
     * @return string
     */
    public function getLongMessage();

    /**
     * Return the severity of the error
     * @return string
     */
    public function getSeverity();
}