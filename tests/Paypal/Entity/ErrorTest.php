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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/paypal Project website
 *
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     0.8.2
 */

namespace UniAlteri\tests\Paypal\Entity;

use UniAlteri\Paypal\Express\Service\Error;

/**
 * Class ErrorTest.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/paypal Project website
 *
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
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
     * @covers UniAlteri\Paypal\Express\Service\Error::getCode()
     */
    public function testGetCode()
    {
        $this->assertNull($this->generateError()->getCode());
        $this->assertEquals('fooBar', $this->generateError('fooBar')->getCode());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\Error::getShortMessage()
     */
    public function testGetShortMessage()
    {
        $this->assertNull($this->generateError()->getShortMessage());
        $this->assertEquals('fooBar', $this->generateError(null, 'fooBar')->getShortMessage());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\Error::getLongMessage()
     */
    public function testGetLongMessage()
    {
        $this->assertNull($this->generateError()->getLongMessage());
        $this->assertEquals('fooBar', $this->generateError(null, null, 'fooBar')->getLongMessage());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\Error::getSeverity()
     */
    public function testGetSeverity()
    {
        $this->assertNull($this->generateError()->getSeverity());
        $this->assertEquals('fooBar', $this->generateError(null, null, null, 'fooBar')->getSeverity());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\Error::__construct()
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('UniAlteri\Paypal\Express\Service\Error', $this->generateError('code', 'sort', 'long', 'fooBar'));
    }
}
