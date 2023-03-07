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
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 *
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 *
 */

declare(strict_types=1);

namespace Teknoo\Tests\Paypal\Transport;

use PHPUnit\Framework\TestCase;
use Teknoo\Paypal\Express\Contracts\PurchaseItemInterface;
use Teknoo\Paypal\Express\Transport\ArgumentBag;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 *
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\Paypal\Express\Transport\ArgumentBag
 */
class ArgumentBagTest extends TestCase
{
    private function generateObject($args = []): ArgumentBag
    {
        return new ArgumentBag($args);
    }

    public function testConstruct()
    {
        self::assertInstanceOf(ArgumentBag::class, $this->generateObject());
        self::assertInstanceOf(ArgumentBag::class, $this->generateObject(['foo' => 'bar']));
    }

    public function testReset()
    {
        $object = $this->generateObject();
        $object->set('foo', 'bar');
        $object->reset();
        $array = $object->toArray();
        self::assertEquals(0, \count($array));
    }

    public function testSetFailure()
    {
        $this->expectException(\TypeError::class);

        $object = $this->generateObject();
        $object->set(new \stdClass(), 'bar');
    }

    public function testSet()
    {
        $object = $this->generateObject();
        $object->set('foo', 'bar');
        $array = $object->toArray();
        self::assertEquals(1, \count($array));
        self::assertEquals('bar', $array['foo']);
    }

    public function testGetFailure()
    {
        $this->expectException(\TypeError::class);

        $object = $this->generateObject();
        $object->get(new \stdClass());
    }

    public function testGetFailureNotFound()
    {
        $this->expectException(\RuntimeException::class);
        $object = $this->generateObject();
        $object->get('notFound');
    }

    public function testGet()
    {
        $object = $this->generateObject();
        $object->set('foo', 'bar');
        self::assertEquals('bar', $object->get('foo'));
    }

    public function testToArray()
    {
        $object = $this->generateObject();
        $object->set('foo', 'bar');
        $array = $object->toArray();
        self::assertEquals(1, \count($array));
        self::assertEquals('bar', $array['foo']);
    }

    public function testAddItem()
    {
        $item1 = $this->createMock(PurchaseItemInterface::class);
        $item1->expects(self::any())->method('getName')->willReturn('name 1');
        $item1->expects(self::any())->method('getDescription')->willReturn('desc 1');
        $item1->expects(self::any())->method('getAmount')->willReturn(123.0);
        $item1->expects(self::any())->method('getQantity')->willReturn(1);
        $item1->expects(self::any())->method('getReference')->willReturn('n1234');
        $item1->expects(self::any())->method('getRequestUrl')->willReturn('https://foo.bar');
        $item1->expects(self::any())->method('getItemCategory')->willReturn('Digital');

        $item2 = $this->createMock(PurchaseItemInterface::class);
        $item2->expects(self::any())->method('getName')->willReturn('name 2');
        $item2->expects(self::any())->method('getDescription')->willReturn('');
        $item2->expects(self::any())->method('getAmount')->willReturn(456.0);
        $item2->expects(self::any())->method('getQantity')->willReturn(3);
        $item2->expects(self::any())->method('getReference')->willReturn('');
        $item2->expects(self::any())->method('getRequestUrl')->willReturn('');
        $item2->expects(self::any())->method('getItemCategory')->willReturn('Physical');

        $object = $this->generateObject();
        self::assertEquals($object, $object->addItem($item1));
        self::assertEquals($object, $object->addItem($item2));

        $array = $object->toArray();
        self::assertEquals(
            [
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
            ],
            $array
        );
    }
}
