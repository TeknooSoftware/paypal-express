<?php

/*
 * Paypal Express.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 *
 * @author      Richard Déloge <richard@teknoo.software>
 *
 *
 */

declare(strict_types=1);

namespace Teknoo\Tests\Paypal\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Teknoo\Paypal\Express\Service\Error;
use Teknoo\Paypal\Express\Service\TransactionResult;

/**
 * Class TransactionResultTest.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 *
 * @author      Richard Déloge <richard@teknoo.software>
 *
 */
#[CoversClass(TransactionResult::class)]
class TransactionResultTest extends TestCase
{
    private function generateObject(array $param): TransactionResult
    {
        return new TransactionResult($param);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(TransactionResult::class, $this->generateObject([]));
    }

    public function testGetAckValueFailure(): void
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getAckValue();
    }

    public function testGetAckValue(): void
    {
        $this->assertEquals('fooBar', $this->generateObject(['ACK' => 'fooBar'])->getAckValue());
    }

    public function testIsSuccessfulFailure(): void
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->isSuccessful();
    }

    public function testIsSuccessful(): void
    {
        $this->assertFalse($this->generateObject(['ACK' => 'fooBar'])->isSuccessful());
        $this->assertTrue($this->generateObject(['ACK' => 'SUCCESS'])->isSuccessful());
        $this->assertTrue($this->generateObject(['ACK' => 'SUCCESSWITHWARNING'])->isSuccessful());
    }

    public function testGetTokenValueFailure(): void
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getTokenValue();
    }

    public function testGetTokenValue(): void
    {
        $this->assertEquals('fooBar', $this->generateObject(['TOKEN' => 'fooBar'])->getTokenValue());
    }

    public function testGetPayerIdValueFailure(): void
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getPayerIdValue();
    }

    public function testGetPayerIdValue(): void
    {
        $this->assertEquals('fooBar', $this->generateObject(['PAYERID' => 'fooBar'])->getPayerIdValue());
    }

    public function testGetTimestampValueFailure(): void
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getTimestampValue();
    }

    public function testGetTimestampValue(): void
    {
        $this->assertEquals('fooBar', $this->generateObject(['TIMESTAMP' => 'fooBar'])->getTimestampValue());
    }

    public function testGetCorrelationIdValueFailure(): void
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getCorrelationIdValue();
    }

    public function testGetCorrelationIdValue(): void
    {
        $this->assertEquals('fooBar', $this->generateObject(['CORRELATIONID' => 'fooBar'])->getCorrelationIdValue());
    }

    public function testGetVersionValueFailure(): void
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getVersionValue();
    }

    public function testGetVersionValue(): void
    {
        $this->assertEquals('fooBar', $this->generateObject(['VERSION' => 'fooBar'])->getVersionValue());
    }

    public function testGetBuildValueFailure(): void
    {
        $this->expectException(\Exception::class);
        $this->generateObject([])->getBuildValue();
    }

    public function testGetBuildValue(): void
    {
        $this->assertEquals('fooBar', $this->generateObject(['BUILD' => 'fooBar'])->getBuildValue());
    }

    public function testGetErrorsEmpty(): void
    {
        $this->assertEmpty($this->generateObject([])->getErrors());
    }

    public function testGetErrorsOne(): void
    {
        $errors = $this->generateObject(
            [
                'L_ERRORCODE0' => 123,
                'L_SHORTMESSAGE0' => 'shortMess',
                'L_LONGMESSAGE0' => 'longMess',
                'L_SEVERITYCODE0' => 'warning',
            ]
        )->getErrors();

        $this->assertCount(1, $errors);
        $this->assertInstanceOf(Error::class, $errors[0]);
        $this->assertEquals(123, $errors[0]->getCode());
        $this->assertEquals('shortMess', $errors[0]->getShortMessage());
        $this->assertEquals('longMess', $errors[0]->getLongMessage());
        $this->assertEquals('warning', $errors[0]->getSeverity());
    }

    public function testGetErrorsTwo(): void
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

        $this->assertCount(2, $errors);
        $this->assertInstanceOf(Error::class, $errors[0]);
        $this->assertEquals(123, $errors[0]->getCode());
        $this->assertEquals('shortMess', $errors[0]->getShortMessage());
        $this->assertEquals('longMess', $errors[0]->getLongMessage());
        $this->assertEquals('warning', $errors[0]->getSeverity());
        $this->assertInstanceOf(Error::class, $errors[1]);
        $this->assertEquals(456, $errors[1]->getCode());
        $this->assertEquals('shortMess2', $errors[1]->getShortMessage());
        $this->assertEquals('longMess2', $errors[1]->getLongMessage());
        $this->assertEquals('warning2', $errors[1]->getSeverity());
    }

    public function testGetErrorsOneThree(): void
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

        $this->assertCount(1, $errors);
        $this->assertInstanceOf(Error::class, $errors[0]);
        $this->assertEquals(123, $errors[0]->getCode());
        $this->assertEquals('shortMess', $errors[0]->getShortMessage());
        $this->assertEquals('longMess', $errors[0]->getLongMessage());
        $this->assertEquals('warning', $errors[0]->getSeverity());
    }

    public function testGetRawValues(): void
    {
        $array = $this->generateObject(['foo' => 'bar', 'hello' => 'world'])->getRawValues();
        $this->assertEquals(['foo' => 'bar', 'hello' => 'world'], $array);
    }
}
