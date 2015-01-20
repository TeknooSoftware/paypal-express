<?php

namespace UniAlteri\Paypal\Express\Transport;

/**
 * Interface ArgumentBagInterface
 * Interface to define arguments container for all request to the api
 * @package UniAlteri\Paypal\Express\Transport
 */
interface ArgumentBagInterface
{
    /**
     * Reset this bag
     *
     * @return $this
     */
    public function reset();

    /**
     * Define an argument in the bag
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value);

    /**
     * Return an argument defined in the bag
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * Return the list of argument as an array object
     * @return \ArrayAccess
     */
    public function toArray();
}