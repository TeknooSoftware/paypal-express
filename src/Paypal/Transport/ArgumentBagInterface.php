<?php

/**
 * Paypal Express.
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
 *
 * @link        http://teknoo.it/paypal Project website
 *
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     0.8.3
 */

namespace UniAlteri\Paypal\Express\Transport;

/**
 * Interface ArgumentBagInterface
 * Interface to define arguments container for all request to the api.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/paypal Project website
 *
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
interface ArgumentBagInterface
{
    /**
     * Reset this bag.
     *
     * @return $this
     */
    public function reset();

    /**
     * Define an argument in the bag.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     *
     * @throws \InvalidArgumentException when $name is not a string
     */
    public function set($name, $value);

    /**
     * Return an argument defined in the bag.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException when $name is not a string
     */
    public function get($name);

    /**
     * Return the list of argument as an array object.
     *
     * @return \ArrayAccess|\Countable
     */
    public function toArray();
}
