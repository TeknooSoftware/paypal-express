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

/**
 * Class Error
 * To manipulate errors from paypal.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/paypal Project website
 *
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
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
