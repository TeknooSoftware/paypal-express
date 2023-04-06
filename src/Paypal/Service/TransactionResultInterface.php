<?php

/*
 * Paypal Express.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * it is available in LICENSE file at the root of this package
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

/**
 * Interface to define object parsing paypal return.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
interface TransactionResultInterface
{
    /*
     * Return the raw value of the ACK field from the paypal API for this transaction.
     */
    public function getAckValue(): string;

    /*
     * Return a boolean to test if the operation via the api is successful.
     */
    public function isSuccessful(): bool;

    /*
     * Return the raw value of the Token field from the paypal API for this transaction.
     */
    public function getTokenValue(): string;

    /*
     * Return the raw value of the PayerId field from the paypal API for this transaction.
     */
    public function getPayerIdValue(): string;

    /*
     * Return the raw value of the Timestamp field from the paypal API for this transaction.
     */
    public function getTimestampValue(): string;

    /*
     * Return the raw value of the CorrelationId field from the paypal API for this transaction.
     */
    public function getCorrelationIdValue(): string;

    /*
     * Return the raw value of the Version field from the paypal API for this transaction.
     */
    public function getVersionValue(): string;

    /*
     * Return the raw value of the Build field from the paypal API for this transaction.
     */
    public function getBuildValue(): string;

    /**
     * Return errors from paypal.
     *
     * @return array<ErrorInterface>
     */
    public function getErrors(): array;

    /**
     * Return raw value from the request.
     *
     * @return array<string, mixed>
     */
    public function getRawValues(): array;
}
