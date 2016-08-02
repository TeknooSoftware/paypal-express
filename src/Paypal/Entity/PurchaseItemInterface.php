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
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 *
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @version     0.8.3
 */
namespace Teknoo\Paypal\Express\Entity;

/**
 * Interface PurchaseItemInterface
 * Interface to represent an intem into a purchase in the vendor's informations system.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 *
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface PurchaseItemInterface
{
    /**
     * Get the name about this item in the purchase.
     *
     * @return string
     */
    public function getPaymentRequestName();

    /**
     * Get the description about this item in the purchase.
     *
     * @return string
     */
    public function getPaymentRequestDesc();

    /**
     * Get the amount about this item in the purchase (required).
     *
     * @return float
     */
    public function getPaymentRequestAmount();

    /**
     * Get the quantity about this item in the purchase (required).
     *
     * @return int
     */
    public function getPaymentRequestQantity();

    /**
     * Get the reference about this item in the purchase.
     *
     * @return string
     */
    public function getPaymentRequestNumber();

    /**
     * Get the url about this item in the purchase.
     *
     * @return string
     */
    public function getPaymentRequestUrl();

    /**
     * Get the item category (Digital or Physical).
     *
     * @return string
     */
    public function getPaymentRequestItemCategory();
}
