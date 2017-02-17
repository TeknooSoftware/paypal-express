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
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @version     0.8.3
 */

namespace Teknoo\Paypal\Express\Service;

use Teknoo\Paypal\Express\Entity\PurchaseInterface;

/**
 * Interface ServiceInterface
 * Interface to define available service to use paypal express checkout in php platform.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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
