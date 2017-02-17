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

/**
 * Class Error
 * To manipulate errors from paypal.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Error implements ErrorInterface
{
    /**
     * Code of the error.
     *
     * @var string
     */
    protected $code;

    /**
     * Short message of the error.
     *
     * @var string
     */
    protected $shortMessage;

    /**
     * Long message of the error.
     *
     * @var string
     */
    protected $longMessage;

    /**
     * Severity of the error.
     *
     * @var string
     */
    protected $severity;

    /**
     * Initialize object error.
     *
     * @param string $code
     * @param string $shortMessage
     * @param string $longMessage
     * @param string $severity
     */
    public function __construct($code, $shortMessage, $longMessage, $severity)
    {
        $this->code = $code;
        $this->shortMessage = $shortMessage;
        $this->longMessage = $longMessage;
        $this->severity = $severity;
    }

    /**
     * Get the code of the error.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Return the short message of the error.
     *
     * @return string
     */
    public function getShortMessage()
    {
        return $this->shortMessage;
    }

    /**
     * Return the long message of the error.
     *
     * @return string
     */
    public function getLongMessage()
    {
        return $this->longMessage;
    }

    /**
     * Return the severity of the error.
     *
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }
}
