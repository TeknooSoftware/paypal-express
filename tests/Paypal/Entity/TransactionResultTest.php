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

use UniAlteri\Paypal\Express\Service\TransactionResult;

/**
 * Class TransactionResultTest.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/paypal Project website
 *
 * @license     http://teknoo.it/paypal/license/mit         MIT License
 * @license     http://teknoo.it/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class TransactionResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Generate testable object.
     *
     * @param array $param
     *
     * @return TransactionResult
     */
    protected function generateObject($param)
    {
        return new TransactionResult($param);
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::__construct()
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('UniAlteri\Paypal\Express\Service\TransactionResult', $this->generateObject([]));
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getAckValue()
     */
    public function testGetAckValueFailure()
    {
        try {
            $this->generateObject([])->getAckValue();
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the object must throw an exception when the required value is not defined');
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getAckValue()
     */
    public function testGetAckValue()
    {
        $this->assertEquals('fooBar', $this->generateObject(['ACK' => 'fooBar'])->getAckValue());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::isSuccessful()
     */
    public function testIsSuccessfulFailure()
    {
        try {
            $this->generateObject([])->isSuccessful();
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the object must throw an exception when the required value is not defined');
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::isSuccessful()
     */
    public function testIsSuccessful()
    {
        $this->assertFalse($this->generateObject(['ACK' => 'fooBar'])->isSuccessful());
        $this->assertTrue($this->generateObject(['ACK' => 'SUCCESS'])->isSuccessful());
        $this->assertTrue($this->generateObject(['ACK' => 'SUCCESSWITHWARNING'])->isSuccessful());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getTokenValue()
     */
    public function testGetTokenValueFailure()
    {
        try {
            $this->generateObject([])->getTokenValue();
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the object must throw an exception when the required value is not defined');
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getTokenValue()
     */
    public function testGetTokenValue()
    {
        $this->assertEquals('fooBar', $this->generateObject(['TOKEN' => 'fooBar'])->getTokenValue());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getPayerIdValue()
     */
    public function testGetPayerIdValueFailure()
    {
        try {
            $this->generateObject([])->getPayerIdValue();
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the object must throw an exception when the required value is not defined');
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getPayerIdValue()
     */
    public function testGetPayerIdValue()
    {
        $this->assertEquals('fooBar', $this->generateObject(['PAYERID' => 'fooBar'])->getPayerIdValue());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getTimestampValue()
     */
    public function testGetTimestampValueFailure()
    {
        try {
            $this->generateObject([])->getTimestampValue();
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the object must throw an exception when the required value is not defined');
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getTimestampValue()
     */
    public function testGetTimestampValue()
    {
        $this->assertEquals('fooBar', $this->generateObject(['TIMESTAMP' => 'fooBar'])->getTimestampValue());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getCorrelationIdValue()
     */
    public function testGetCorrelationIdValueFailure()
    {
        try {
            $this->generateObject([])->getCorrelationIdValue();
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the object must throw an exception when the required value is not defined');
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getCorrelationIdValue()
     */
    public function testGetCorrelationIdValue()
    {
        $this->assertEquals('fooBar', $this->generateObject(['CORRELATIONID' => 'fooBar'])->getCorrelationIdValue());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getVersionValue()
     */
    public function testGetVersionValueFailure()
    {
        try {
            $this->generateObject([])->getVersionValue();
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the object must throw an exception when the required value is not defined');
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getVersionValue()
     */
    public function testGetVersionValue()
    {
        $this->assertEquals('fooBar', $this->generateObject(['VERSION' => 'fooBar'])->getVersionValue());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getBuildValue()
     */
    public function testGetBuildValueFailure()
    {
        try {
            $this->generateObject([])->getBuildValue();
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the object must throw an exception when the required value is not defined');
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getBuildValue()
     */
    public function testGetBuildValue()
    {
        $this->assertEquals('fooBar', $this->generateObject(['BUILD' => 'fooBar'])->getBuildValue());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getErrors()
     */
    public function testGetErrorsEmpty()
    {
        $this->assertEmpty($this->generateObject([])->getErrors());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getErrors()
     */
    public function testGetErrorsOne()
    {
        $errors = $this->generateObject(
            [
                'L_ERRORCODE0' => 'err1',
                'L_SHORTMESSAGE0' => 'shortMess',
                'L_LONGMESSAGE0' => 'longMess',
                'L_SEVERITYCODE0' => 'warning',
            ]
        )->getErrors();

        $this->assertEquals(1, count($errors));
        $this->assertInstanceOf('UniAlteri\Paypal\Express\Service\Error', $errors[0]);
        $this->assertEquals('err1', $errors[0]->getCode());
        $this->assertEquals('shortMess', $errors[0]->getShortMessage());
        $this->assertEquals('longMess', $errors[0]->getLongMessage());
        $this->assertEquals('warning', $errors[0]->getSeverity());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getErrors()
     */
    public function testGetErrorsTwo()
    {
        $errors = $this->generateObject(
            [
                'L_ERRORCODE0' => 'err1',
                'L_ERRORCODE1' => 'err2',
                'L_SHORTMESSAGE0' => 'shortMess',
                'L_SHORTMESSAGE1' => 'shortMess2',
                'L_LONGMESSAGE0' => 'longMess',
                'L_LONGMESSAGE1' => 'longMess2',
                'L_SEVERITYCODE0' => 'warning',
                'L_SEVERITYCODE1' => 'warning2',
            ]
        )->getErrors();

        $this->assertEquals(2, count($errors));
        $this->assertInstanceOf('UniAlteri\Paypal\Express\Service\Error', $errors[0]);
        $this->assertEquals('err1', $errors[0]->getCode());
        $this->assertEquals('shortMess', $errors[0]->getShortMessage());
        $this->assertEquals('longMess', $errors[0]->getLongMessage());
        $this->assertEquals('warning', $errors[0]->getSeverity());
        $this->assertInstanceOf('UniAlteri\Paypal\Express\Service\Error', $errors[1]);
        $this->assertEquals('err2', $errors[1]->getCode());
        $this->assertEquals('shortMess2', $errors[1]->getShortMessage());
        $this->assertEquals('longMess2', $errors[1]->getLongMessage());
        $this->assertEquals('warning2', $errors[1]->getSeverity());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getErrors()
     */
    public function testGetErrorsOneThree()
    {
        $errors = $this->generateObject(
            [
                'L_ERRORCODE0' => 'err1',
                'L_ERRORCODE3' => 'err2',
                'L_SHORTMESSAGE0' => 'shortMess',
                'L_SHORTMESSAGE3' => 'shortMess2',
                'L_LONGMESSAGE0' => 'longMess',
                'L_LONGMESSAGE3' => 'longMess2',
                'L_SEVERITYCODE0' => 'warning',
                'L_SEVERITYCODE3' => 'warning2',
            ]
        )->getErrors();

        $this->assertEquals(1, count($errors));
        $this->assertInstanceOf('UniAlteri\Paypal\Express\Service\Error', $errors[0]);
        $this->assertEquals('err1', $errors[0]->getCode());
        $this->assertEquals('shortMess', $errors[0]->getShortMessage());
        $this->assertEquals('longMess', $errors[0]->getLongMessage());
        $this->assertEquals('warning', $errors[0]->getSeverity());
    }

    /**
     * @covers UniAlteri\Paypal\Express\Service\TransactionResult::getRawValues()
     */
    public function testGetRawValues()
    {
        $array = $this->generateObject(['foo' => 'bar', 'hello' => 'world'])->getRawValues();
        $this->assertEquals(['foo' => 'bar', 'hello' => 'world'], $array);
    }
}
