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
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\Paypal\Express\Service;

/**
 * Interface to define object parsing paypal return.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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
