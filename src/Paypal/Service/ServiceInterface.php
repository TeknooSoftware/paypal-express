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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/paypal Project website
 *
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     0.8.3
 */

namespace UniAlteri\Paypal\Express\Service;

use UniAlteri\Paypal\Express\Entity\PurchaseInterface;

/**
 * Interface ServiceInterface
 * Interface to define available service to use paypal express checkout in php platform.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/paypal Project website
 *
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
interface ServiceInterface
{
    /**
     * Prepare a transaction via the Paypal API and get the token to identify
     * the transaction and the consumer on the paypal service.
     *
     * @param PurchaseInterface $purchase
     *
     * @return TransactionResultInterface
     *
     * @throws \RuntimeException if the purchase object is invalid
     * @throws \Exception
     */
    public function generateToken(PurchaseInterface $purchase);

    /**
     * Prepare a transaction via the Paypal API and get the url to redirect
     * the user to paypal service to process of the payment.
     *
     * @param PurchaseInterface $purchase
     *
     * @return string
     */
    public function prepareTransaction(PurchaseInterface $purchase);

    /**
     * Get the transaction result from the Paypal API.
     *
     * @param string $token
     *
     * @return TransactionResultInterface
     */
    public function getTransactionResult($token);

    /**
     * To confirm an active transaction on the Paypal API and unblock amounts.
     *
     * @param string            $token
     * @param string            $payerId
     * @param PurchaseInterface $purchase
     *
     * @return TransactionResultInterface
     */
    public function confirmTransaction($token, $payerId, PurchaseInterface $purchase);
}
