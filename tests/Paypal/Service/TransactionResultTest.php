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
 *
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @version     0.8.3
 */
namespace Teknoo\tests\Paypal\Service;

use Teknoo\Paypal\Express\Service\Error;
use Teknoo\Paypal\Express\Service\TransactionResult;

/**
 * Class TransactionResultTest.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 *
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class TransactionResultTest extends \PHPUnit\Framework\TestCase
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
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::__construct()
     */
    public function testConstruct()
    {
        self::assertInstanceOf('Teknoo\Paypal\Express\Service\TransactionResult', $this->generateObject([]));
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getAckValue()
     * @expectedException \Exception
     */
    public function testGetAckValueFailure()
    {
        $this->generateObject([])->getAckValue();
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getAckValue()
     */
    public function testGetAckValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['ACK' => 'fooBar'])->getAckValue());
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::isSuccessful()
     * @expectedException \Exception
     */
    public function testIsSuccessfulFailure()
    {
        $this->generateObject([])->isSuccessful();
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::isSuccessful()
     */
    public function testIsSuccessful()
    {
        self::assertFalse($this->generateObject(['ACK' => 'fooBar'])->isSuccessful());
        self::assertTrue($this->generateObject(['ACK' => 'SUCCESS'])->isSuccessful());
        self::assertTrue($this->generateObject(['ACK' => 'SUCCESSWITHWARNING'])->isSuccessful());
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getTokenValue()
     * @expectedException \Exception
     */
    public function testGetTokenValueFailure()
    {
        $this->generateObject([])->getTokenValue();
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getTokenValue()
     */
    public function testGetTokenValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['TOKEN' => 'fooBar'])->getTokenValue());
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getPayerIdValue()
     * @expectedException \Exception
     */
    public function testGetPayerIdValueFailure()
    {
        $this->generateObject([])->getPayerIdValue();
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getPayerIdValue()
     */
    public function testGetPayerIdValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['PAYERID' => 'fooBar'])->getPayerIdValue());
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getTimestampValue()
     * @expectedException \Exception
     */
    public function testGetTimestampValueFailure()
    {
        $this->generateObject([])->getTimestampValue();
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getTimestampValue()
     */
    public function testGetTimestampValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['TIMESTAMP' => 'fooBar'])->getTimestampValue());
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getCorrelationIdValue()
     * @expectedException \Exception
     */
    public function testGetCorrelationIdValueFailure()
    {
        $this->generateObject([])->getCorrelationIdValue();
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getCorrelationIdValue()
     */
    public function testGetCorrelationIdValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['CORRELATIONID' => 'fooBar'])->getCorrelationIdValue());
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getVersionValue()
     * @expectedException \Exception
     */
    public function testGetVersionValueFailure()
    {
        $this->generateObject([])->getVersionValue();
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getVersionValue()
     */
    public function testGetVersionValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['VERSION' => 'fooBar'])->getVersionValue());
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getBuildValue()
     * @expectedException \Exception
     */
    public function testGetBuildValueFailure()
    {
        $this->generateObject([])->getBuildValue();
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getBuildValue()
     */
    public function testGetBuildValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['BUILD' => 'fooBar'])->getBuildValue());
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getErrors()
     */
    public function testGetErrorsEmpty()
    {
        self::assertEmpty($this->generateObject([])->getErrors());
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getErrors()
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

        self::assertEquals(1, count($errors));
        self::assertInstanceOf(Error::class, $errors[0]);
        self::assertEquals('err1', $errors[0]->getCode());
        self::assertEquals('shortMess', $errors[0]->getShortMessage());
        self::assertEquals('longMess', $errors[0]->getLongMessage());
        self::assertEquals('warning', $errors[0]->getSeverity());
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getErrors()
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

        self::assertEquals(2, count($errors));
        self::assertInstanceOf(Error::class, $errors[0]);
        self::assertEquals('err1', $errors[0]->getCode());
        self::assertEquals('shortMess', $errors[0]->getShortMessage());
        self::assertEquals('longMess', $errors[0]->getLongMessage());
        self::assertEquals('warning', $errors[0]->getSeverity());
        self::assertInstanceOf(Error::class, $errors[1]);
        self::assertEquals('err2', $errors[1]->getCode());
        self::assertEquals('shortMess2', $errors[1]->getShortMessage());
        self::assertEquals('longMess2', $errors[1]->getLongMessage());
        self::assertEquals('warning2', $errors[1]->getSeverity());
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getErrors()
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

        self::assertEquals(1, count($errors));
        self::assertInstanceOf(Error::class, $errors[0]);
        self::assertEquals('err1', $errors[0]->getCode());
        self::assertEquals('shortMess', $errors[0]->getShortMessage());
        self::assertEquals('longMess', $errors[0]->getLongMessage());
        self::assertEquals('warning', $errors[0]->getSeverity());
    }

    /**
     * @covers \Teknoo\Paypal\Express\Service\TransactionResult::getRawValues()
     */
    public function testGetRawValues()
    {
        $array = $this->generateObject(['foo' => 'bar', 'hello' => 'world'])->getRawValues();
        self::assertEquals(['foo' => 'bar', 'hello' => 'world'], $array);
    }
}
