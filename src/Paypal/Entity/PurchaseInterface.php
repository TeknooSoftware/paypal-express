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

namespace UniAlteri\Paypal\Express\Entity;

/**
 * Interface PurchaseInterface
 * Interface to represent a purchase in the vendor's informations system
 * to communicate data to Paypal API.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/paypal Project website
 *
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
interface PurchaseInterface
{
    /**
     * Get the amount of the purchase, feet included, in float representation.
     *
     * @return float
     */
    public function getAmount();

    /**
     * Get the payment action to use in the transaction (sale, ..).
     *
     * @return string
     */
    public function getPaymentAction();

    /**
     * Get the url to redirect the consumer after the payment operation.
     *
     * @return string
     */
    public function getReturnUrl();

    /**
     * Get the url to redirect the consumer when it cancel the transaction in paypal.
     *
     * @return string
     */
    public function getCancelUrl();

    /**
     * Get the currency used for this transaction.
     *
     * @return string
     */
    public function getCurrencyCode();

    /**
     * Get the consumer of this transaction.
     *
     * @return ConsumerInterface
     */
    public function getConsumer();
}
