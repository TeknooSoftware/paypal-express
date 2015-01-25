<?php

namespace UniAlteri\Paypal\Express\Transport;

/**
 * Class ArgumentBag
 * Class to allow developer to pass arguments for request
 * @package UniAlteri\Paypal\Express\Transport
 */
class ArgumentBag implements ArgumentBagInterface
{
    /**
     * @var \ArrayObject
     */
    protected $parameters;

    /**
     * To initialize this bag
     */
    public function __construct($parameters=null)
    {
        $this->reset();
        if (!empty($parameters)) {
            $this->parameters->exchangeArray($parameters);
        }
    }

    /**
     * Reset this bag
     *
     * @return $this
     */
    public function reset()
    {
        $this->parameters = new \ArrayObject();

        return $this;
    }

    /**
     * Define an argument in the bag
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     * @throws \InvalidArgumentException when $name is not a string
     */
    public function set($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('The name is not a string');
        }

        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * Return an argument defined in the bag
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException when $name is not a string
     */
    public function get($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('The name is not a string');
        }

        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }

        throw new \RuntimeException(sprintf('Error, the required parameter %s is not defined', $name));
    }

    /**
     * Return the list of argument as an array object
     * @return \ArrayAccess|\Countable
     */
    public function toArray()
    {
        return $this->parameters;
    }
}