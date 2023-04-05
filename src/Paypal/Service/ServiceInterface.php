<?php

/*
 * Paypal Express.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Paypal\Express\Service;

use Teknoo\Paypal\Express\Contracts\PurchaseInterface;

/**
 * Interface to define available service to use paypal express checkout in php platform.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
interface ServiceInterface
{
    /**
     * Prepare a transaction via the Paypal API and get the token to identify
     * the transaction and the consumer on the paypal service.
     *
     * @throws \RuntimeException if the purchase object is invalid
     * @throws \Exception
     */
    public function generateToken(PurchaseInterface $purchase): TransactionResultInterface;

    /*
     * Prepare a transaction via the Paypal API and get the url to redirect
     * the user to paypal service to process of the payment.
     */
    public function prepareTransaction(PurchaseInterface $purchase): string;

    /*
     * Get the transaction result from the Paypal API.
     */
    public function getTransactionResult(string $token): TransactionResultInterface;

    /*
     * To confirm an active transaction on the Paypal API and unblock amounts.
     */
    public function confirmTransaction(
        string $token,
        string $payerId,
        PurchaseInterface $purchase
    ): TransactionResultInterface;
}
