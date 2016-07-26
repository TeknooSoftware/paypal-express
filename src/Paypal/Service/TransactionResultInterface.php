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
 * @license     http://teknoo.software/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @version     0.8.3
 */
namespace Teknoo\Paypal\Express\Service;

/**
 * Interface TransactionResultInterface
 * Interface to define object parsing paypal return.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @license     http://teknoo.software/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface TransactionResultInterface
{
    /**
     * Return the raw value of the ACK field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getAckValue();

    /**
     * Return a boolean to test if the operation via the api is successful.
     *
     * @return bool
     */
    public function isSuccessful();

    /**
     * Return the raw value of the Token field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getTokenValue();

    /**
     * Return the raw value of the PayerId field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getPayerIdValue();

    /**
     * Return the raw value of the Timestamp field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getTimestampValue();

    /**
     * Return the raw value of the CorrelationId field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getCorrelationIdValue();

    /**
     * Return the raw value of the Version field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getVersionValue();

    /**
     * Return the raw value of the Build field from the paypal API for this transaction.
     *
     * @return string
     */
    public function getBuildValue();

    /**
     * Return errors from paypal.
     *
     * @return ErrorInterface[]
     */
    public function getErrors();

    /**
     * Return raw value from the request.
     *
     * @return mixed
     */
    public function getRawValues();
}
