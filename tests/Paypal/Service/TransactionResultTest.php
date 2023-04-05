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
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 *
 * @author      Richard Déloge <richard@teknoo.software>
 *
 *
 */

declare(strict_types=1);

namespace Teknoo\Tests\Paypal\Service;

use PHPUnit\Framework\TestCase;
use Teknoo\Paypal\Express\Service\Error;
use Teknoo\Paypal\Express\Service\TransactionResult;

/**
 * Class TransactionResultTest.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 *
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @covers \Teknoo\Paypal\Express\Service\TransactionResult
 */
class TransactionResultTest extends TestCase
{
    private function generateObject(array $param): TransactionResult
    {
        return new TransactionResult($param);
    }

    public function testConstruct()
    {
        self::assertInstanceOf(TransactionResult::class, $this->generateObject([]));
    }

    public function testGetAckValueFailure()
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getAckValue();
    }

    public function testGetAckValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['ACK' => 'fooBar'])->getAckValue());
    }

    public function testIsSuccessfulFailure()
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->isSuccessful();
    }

    public function testIsSuccessful()
    {
        self::assertFalse($this->generateObject(['ACK' => 'fooBar'])->isSuccessful());
        self::assertTrue($this->generateObject(['ACK' => 'SUCCESS'])->isSuccessful());
        self::assertTrue($this->generateObject(['ACK' => 'SUCCESSWITHWARNING'])->isSuccessful());
    }

    public function testGetTokenValueFailure()
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getTokenValue();
    }

    public function testGetTokenValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['TOKEN' => 'fooBar'])->getTokenValue());
    }

    public function testGetPayerIdValueFailure()
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getPayerIdValue();
    }

    public function testGetPayerIdValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['PAYERID' => 'fooBar'])->getPayerIdValue());
    }

    public function testGetTimestampValueFailure()
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getTimestampValue();
    }

    public function testGetTimestampValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['TIMESTAMP' => 'fooBar'])->getTimestampValue());
    }

    public function testGetCorrelationIdValueFailure()
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getCorrelationIdValue();
    }

    public function testGetCorrelationIdValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['CORRELATIONID' => 'fooBar'])->getCorrelationIdValue());
    }

    public function testGetVersionValueFailure()
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getVersionValue();
    }

    public function testGetVersionValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['VERSION' => 'fooBar'])->getVersionValue());
    }

    public function testGetBuildValueFailure()
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getBuildValue();
    }

    public function testGetBuildValue()
    {
        self::assertEquals('fooBar', $this->generateObject(['BUILD' => 'fooBar'])->getBuildValue());
    }

    public function testGetErrorsEmpty()
    {
        self::assertEmpty($this->generateObject([])->getErrors());
    }

    public function testGetErrorsOne()
    {
        $errors = $this->generateObject(
            [
                'L_ERRORCODE0' => 123,
                'L_SHORTMESSAGE0' => 'shortMess',
                'L_LONGMESSAGE0' => 'longMess',
                'L_SEVERITYCODE0' => 'warning',
            ]
        )->getErrors();

        self::assertEquals(1, count($errors));
        self::assertInstanceOf(Error::class, $errors[0]);
        self::assertEquals(123, $errors[0]->getCode());
        self::assertEquals('shortMess', $errors[0]->getShortMessage());
        self::assertEquals('longMess', $errors[0]->getLongMessage());
        self::assertEquals('warning', $errors[0]->getSeverity());
    }

    public function testGetErrorsTwo()
    {
        $errors = $this->generateObject(
            [
                'L_ERRORCODE0' => 123,
                'L_ERRORCODE1' => 456,
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
        self::assertEquals(123, $errors[0]->getCode());
        self::assertEquals('shortMess', $errors[0]->getShortMessage());
        self::assertEquals('longMess', $errors[0]->getLongMessage());
        self::assertEquals('warning', $errors[0]->getSeverity());
        self::assertInstanceOf(Error::class, $errors[1]);
        self::assertEquals(456, $errors[1]->getCode());
        self::assertEquals('shortMess2', $errors[1]->getShortMessage());
        self::assertEquals('longMess2', $errors[1]->getLongMessage());
        self::assertEquals('warning2', $errors[1]->getSeverity());
    }

    public function testGetErrorsOneThree()
    {
        $errors = $this->generateObject(
            [
                'L_ERRORCODE0' => 123,
                'L_ERRORCODE3' => 456,
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
        self::assertEquals(123, $errors[0]->getCode());
        self::assertEquals('shortMess', $errors[0]->getShortMessage());
        self::assertEquals('longMess', $errors[0]->getLongMessage());
        self::assertEquals('warning', $errors[0]->getSeverity());
    }

    public function testGetRawValues()
    {
        $array = $this->generateObject(['foo' => 'bar', 'hello' => 'world'])->getRawValues();
        self::assertEquals(['foo' => 'bar', 'hello' => 'world'], $array);
    }
}
