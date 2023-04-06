<?php

/*
 * Paypal Express.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * that are bundled with this package in the folder licences
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

namespace Teknoo\Paypal\Express\Contracts;

use Teknoo\Paypal\Express\Transport\ArgumentBag;
use Teknoo\Paypal\Express\Transport\ArgumentBagInterface;

/**
 * Interface to represent a purchase in the vendor's informations system
 * to communicate data to Paypal API.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
interface PurchaseInterface
{
    public function getAmount(): float;

    /*
     * Get the payment action to use in the transaction (sale, ..).
     */
    public function getPaymentAction(): string;

    /*
     * Get the url to redirect the consumer after the payment operation.
     */
    public function getReturnUrl(): string;

    /*
     * Get the url to redirect the consumer when it cancel the transaction in paypal.
     */
    public function getCancelUrl(): string;

    /*
     * Get the currency used for this transaction.
     */
    public function getCurrencyCode(): string;

    public function getConsumer(): ConsumerInterface;

    public function configureArgumentBag(ArgumentBagInterface $argumentBag): self;
}
