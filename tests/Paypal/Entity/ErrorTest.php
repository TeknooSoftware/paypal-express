<?php

namespace UniAlteri\Tests\Paypal\Entity;

use UniAlteri\Paypal\Express\Service\Error;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Build the object to test
     * @param string|null $code
     * @param string|null $shortMessage
     * @param string|null $longMessage
     * @param string|null $severity
     * @return Error
     */
    protected function generateError($code=null, $shortMessage=null, $longMessage=null, $severity=null)
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