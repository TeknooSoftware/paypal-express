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
use UniAlteri\Paypal\Express\Entity\PurchaseInterface;

/**
 * Class Purchase
 * Demo business class representing a purchase.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/paypal Project website
 *
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Purchase implements PurchaseInterface
{
    /**
     * @var string
     */
    protected $baseUrl = null;

    /**
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get the amount of the purchase, feet included, in float representation.
     *
     * @return float
     */
    public function getAmount()
    {
        return 314.15;
    }

    /**
     * Get the payment action to use in the transaction (sale, ..).
     *
     * @return string
     */
    public function getPaymentAction()
    {
        return 'SALE';
    }

    /**
     * Get the url to redirect the consumer after the payment operation.
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->baseUrl.'?method=return';
    }

    /**
     * Get the url to redirect the consumer when it cancel the transaction in paypal.
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->baseUrl.'?method=cancel';
    }

    /**
     * Get the currency used for this transaction.
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return 'EUR';
    }

    /**
     * Get the consumer of this transaction.
     *
     * @return ConsumerInterface
     */
    public function getConsumer()
    {
        return new Consumer();
    }
}
