<?php

/*
 * Paypal Express.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * that are bundled with this package in the folder licences
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

/**
 * Class ErrorTest.
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
 * @covers \Teknoo\Paypal\Express\Service\Error
 */
class ErrorTest extends TestCase
{
    private function generateError(
        int $code = 0,
        string $shortMessage = '',
        string $longMessage = '',
        string $severity = ''
    ): Error {
        return new Error($code, $shortMessage, $longMessage, $severity);
    }

    public function testGetCode()
    {
        self::assertEmpty($this->generateError()->getCode());
        self::assertEquals(123, $this->generateError(123)->getCode());
    }

    public function testGetShortMessage()
    {
        self::assertEmpty($this->generateError()->getShortMessage());
        self::assertEquals('fooBar', $this->generateError(123, 'fooBar')->getShortMessage());
    }

    public function testGetLongMessage()
    {
        self::assertEmpty($this->generateError()->getLongMessage());
        self::assertEquals('fooBar', $this->generateError(123, '', 'fooBar')->getLongMessage());
    }

    public function testGetSeverity()
    {
        self::assertEmpty($this->generateError()->getSeverity());
        self::assertEquals('fooBar', $this->generateError(123, '', '', 'fooBar')->getSeverity());
    }

    public function testConstruct()
    {
        self::assertInstanceOf(Error::class, $this->generateError(123, 'sort', 'long', 'fooBar'));
    }
}
