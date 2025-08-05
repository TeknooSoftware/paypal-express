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

namespace Teknoo\Tests\Paypal\Transport;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Teknoo\Paypal\Express\Contracts\PurchaseItemInterface;
use Teknoo\Paypal\Express\Transport\ArgumentBag;

/**
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
#[CoversClass(ArgumentBag::class)]
class ArgumentBagTest extends TestCase
{
    private function generateObject(array $args = []): ArgumentBag
    {
        return new ArgumentBag($args);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(ArgumentBag::class, $this->generateObject());
        $this->assertInstanceOf(ArgumentBag::class, $this->generateObject(['foo' => 'bar']));
    }

    public function testReset(): void
    {
        $object = $this->generateObject();
        $object->set('foo', 'bar');
        $object->reset();
        $array = $object->toArray();
        $this->assertCount(0, $array);
    }

    public function testSetFailure(): void
    {
        $this->expectException(\TypeError::class);

        $object = $this->generateObject();
        $object->set(new \stdClass(), 'bar');
    }

    public function testSet(): void
    {
        $object = $this->generateObject();
        $object->set('foo', 'bar');
        $array = $object->toArray();
        $this->assertCount(1, $array);
        $this->assertEquals('bar', $array['foo']);
    }

    public function testGetFailure(): void
    {
        $this->expectException(\TypeError::class);

        $object = $this->generateObject();
        $object->get(new \stdClass());
    }

    public function testGetFailureNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $object = $this->generateObject();
        $object->get('notFound');
    }

    public function testGet(): void
    {
        $object = $this->generateObject();
        $object->set('foo', 'bar');
        $this->assertEquals('bar', $object->get('foo'));
    }

    public function testToArray(): void
    {
        $object = $this->generateObject();
        $object->set('foo', 'bar');
        $array = $object->toArray();
        $this->assertCount(1, $array);
        $this->assertEquals('bar', $array['foo']);
    }

    public function testAddItem(): void
    {
        $item1 = $this->createMock(PurchaseItemInterface::class);
        $item1->method('getName')->willReturn('name 1');
        $item1->method('getDescription')->willReturn('desc 1');
        $item1->method('getAmount')->willReturn(123.0);
        $item1->method('getQantity')->willReturn(1);
        $item1->method('getReference')->willReturn('n1234');
        $item1->method('getRequestUrl')->willReturn('https://foo.bar');
        $item1->method('getItemCategory')->willReturn('Digital');

        $item2 = $this->createMock(PurchaseItemInterface::class);
        $item2->method('getName')->willReturn('name 2');
        $item2->method('getDescription')->willReturn('');
        $item2->method('getAmount')->willReturn(456.0);
        $item2->method('getQantity')->willReturn(3);
        $item2->method('getReference')->willReturn('');
        $item2->method('getRequestUrl')->willReturn('');
        $item2->method('getItemCategory')->willReturn('Physical');

        $object = $this->generateObject();
        $this->assertEquals($object, $object->addItem($item1));
        $this->assertEquals($object, $object->addItem($item2));

        $array = $object->toArray();
        $this->assertEquals([
            'L_PAYMENTREQUEST_0_NAME0' => 'name 1',
            'L_PAYMENTREQUEST_0_DESC0' => 'desc 1',
            'L_PAYMENTREQUEST_0_AMT0' => 123,
            'L_PAYMENTREQUEST_0_QTY0' => 1,
            'L_PAYMENTREQUEST_0_NUMBER0' => 'n1234',
            'L_PAYMENTREQUEST_0_ITEMURL0' => 'https://foo.bar',
            'L_PAYMENTREQUEST_0_ITEMCATEGORY0' => 'Digital',
            'L_PAYMENTREQUEST_0_NAME1' => 'name 2',
            'L_PAYMENTREQUEST_0_DESC1' => '',
            'L_PAYMENTREQUEST_0_AMT1' => 456,
            'L_PAYMENTREQUEST_0_QTY1' => 3,
            'L_PAYMENTREQUEST_0_NUMBER1' => '',
            'L_PAYMENTREQUEST_0_ITEMURL1' => '',
            'L_PAYMENTREQUEST_0_ITEMCATEGORY1' => 'Physical',
        ], $array);
    }
}
