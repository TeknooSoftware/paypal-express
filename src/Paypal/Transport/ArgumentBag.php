<?php
/**
 * Paypal Express
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/paypal Project website
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.8.2
 */
namespace UniAlteri\Paypal\Express\Transport;

/**
 * Class ArgumentBag
 * Class to allow developer to pass arguments for request
 * @package UniAlteri\Paypal\Express\Transport
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/paypal Project website
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
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
    public function __construct($parameters = null)
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
     * @param  string                    $name
     * @param  mixed                     $value
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
     * @param  string                    $name
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