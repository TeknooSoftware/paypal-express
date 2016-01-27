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

namespace Teknoo\tests\Paypal\Service;

use Teknoo\Paypal\Express\Service\Error;

/**
 * Class ErrorTest.
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
class ErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Build the object to test.
     *
     * @param string|null $code
     * @param string|null $shortMessage
     * @param string|null $longMessage
     * @param string|null $severity
     *
     * @return Error
     */
    protected function generateError($code = null, $shortMessage = null, $longMessage = null, $severity = null)
    {
        return new Error($code, $shortMessage, $longMessage, $severity);
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\Error::getCode()
     */
    public function testGetCode()
    {
        $this->assertNull($this->generateError()->getCode());
        $this->assertEquals('fooBar', $this->generateError('fooBar')->getCode());
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\Error::getShortMessage()
     */
    public function testGetShortMessage()
    {
        $this->assertNull($this->generateError()->getShortMessage());
        $this->assertEquals('fooBar', $this->generateError(null, 'fooBar')->getShortMessage());
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\Error::getLongMessage()
     */
    public function testGetLongMessage()
    {
        $this->assertNull($this->generateError()->getLongMessage());
        $this->assertEquals('fooBar', $this->generateError(null, null, 'fooBar')->getLongMessage());
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\Error::getSeverity()
     */
    public function testGetSeverity()
    {
        $this->assertNull($this->generateError()->getSeverity());
        $this->assertEquals('fooBar', $this->generateError(null, null, null, 'fooBar')->getSeverity());
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\Error::__construct()
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('Teknoo\Paypal\Express\Service\Error', $this->generateError('code', 'sort', 'long', 'fooBar'));
    }
}
