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
 * @version     0.8.2
 */

namespace Acme\demo;

use UniAlteri\Paypal\Express\Entity\ConsumerInterface;

/**
 * Class Consumer
 * Demo business class to represent a consumer.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/paypal Project website
 *
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Consumer implements ConsumerInterface
{
    /**
     * Return the consumer name from the vendor'si nformation system.
     *
     * @return string
     */
    public function getConsumerName()
    {
        return 'Richard Deloge';
    }

    /**
     * Return the consumer address from the vendor's information system.
     *
     * @return string
     */
    public function getShippingAddress()
    {
        return '1 rue de Bruxelles';
    }

    /**
     * Return the consumer extra address from the vendor's information system
     * to allow consumer to input additional inforlations.
     *
     * @return string
     */
    public function getShippingExtraAddress()
    {
        return 'Le Colisee';
    }

    /**
     * Return the consumer zip from the vendor'sinformation system.
     *
     * @return string
     */
    public function getShippingZip()
    {
        return 14120;
    }

    /**
     * Return the consumer city from the vendor'sinformation system.
     *
     * @return string
     */
    public function getShippingCity()
    {
        return 'Mondeville';
    }

    /**
     * Return the consumer state from the vendor'sinformation system
     * (for United State only).
     *
     * @return string
     */
    public function getShippingState()
    {
        return 'Normandy';
    }

    /**
     * Return the consumer country code from the vendor'sinformation system.
     *
     * @return string
     */
    public function getShippingCountryCode()
    {
        return 'France';
    }

    /**     *
     * Return the consumer phone from the vendor'sinformation system.
     *
     * @return string
     */
    public function getPhone()
    {
        return '0033123456789';
    }
}
