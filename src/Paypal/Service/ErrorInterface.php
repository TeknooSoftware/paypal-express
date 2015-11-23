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
 * Interface ErrorInterface
 * Interface to define errors returned by paypal.
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
interface ErrorInterface
{
    /**
     * Get the code of the error.
     *
     * @return string
     */
    public function getCode();

    /**
     * Return the short message of the error.
     *
     * @return string
     */
    public function getShortMessage();

    /**
     * Return the long message of the error.
     *
     * @return string
     */
    public function getLongMessage();

    /**
     * Return the severity of the error.
     *
     * @return string
     */
    public function getSeverity();
}
