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
namespace Teknoo\tests\Paypal\Transport;

use Teknoo\Paypal\Express\Transport\ArgumentBag;

/**
 * Class ArgumentBagTest.
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
class ArgumentBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Generate testable object.
     *
     * @return ArgumentBag
     */
    protected function generateObject($args = null)
    {
        return new ArgumentBag($args);
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\ArgumentBag::__construct()
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('Teknoo\Paypal\Express\Transport\ArgumentBag', $this->generateObject());
        $this->assertInstanceOf('Teknoo\Paypal\Express\Transport\ArgumentBag', $this->generateObject(['foo' => 'bar']));
        $this->assertInstanceOf('Teknoo\Paypal\Express\Transport\ArgumentBag', $this->generateObject(new \ArrayObject(['foo' => 'bar'])));
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\ArgumentBag::reset()
     */
    public function testReset()
    {
        $object = $this->generateObject();
        $object->set('foo', 'bar');
        $object->reset();
        $array = $object->toArray();
        $this->assertEquals(0, $array->count());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\ArgumentBag::set()
     */
    public function testSetFailure()
    {
        $object = $this->generateObject();
        try {
            $object->set(new \stdClass(), 'bar');
        } catch (\InvalidArgumentException $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, if the key name is not a string, the object must throws an exception');
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\ArgumentBag::set()
     */
    public function testSet()
    {
        $object = $this->generateObject();
        $object->set('foo', 'bar');
        $array = $object->toArray();
        $this->assertEquals(1, $array->count());
        $this->assertEquals('bar', $array['foo']);
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\ArgumentBag::get()
     */
    public function testGetFailure()
    {
        $object = $this->generateObject();
        try {
            $object->get(new \stdClass());
        } catch (\InvalidArgumentException $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, if the key name is not a string, the object must throws an exception');
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\ArgumentBag::get()
     */
    public function testGetFailureNotFound()
    {
        $object = $this->generateObject();
        try {
            $object->get('notFound');
        } catch (\RuntimeException $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, if the key name does not exist the object must throws an exception');
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\ArgumentBag::get()
     */
    public function testGet()
    {
        $object = $this->generateObject();
        $object->set('foo', 'bar');
        $this->assertEquals('bar', $object->get('foo'));
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\ArgumentBag::toArray()
     */
    public function testToArray()
    {
        $object = $this->generateObject();
        $object->set('foo', 'bar');
        $array = $object->toArray();
        $this->assertEquals(1, $array->count());
        $this->assertEquals('bar', $array['foo']);
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\ArgumentBag::addItem()
     * @covers Teknoo\Paypal\Express\Transport\ArgumentBag::increasePurchaseItemCounter()
     */
    public function testAddItem()
    {
        $item1 = $this->getMock('Teknoo\Paypal\Express\Entity\PurchaseItemInterface');
        $item1->expects($this->any())->method('getPaymentRequestName')->willReturn('name 1');
        $item1->expects($this->any())->method('getPaymentRequestDesc')->willReturn('desc 1');
        $item1->expects($this->any())->method('getPaymentRequestAmount')->willReturn(123);
        $item1->expects($this->any())->method('getPaymentRequestQantity')->willReturn(1);
        $item1->expects($this->any())->method('getPaymentRequestNumber')->willReturn('n1234');
        $item1->expects($this->any())->method('getPaymentRequestUrl')->willReturn('https://foo.bar');
        $item1->expects($this->any())->method('getPaymentRequestItemCategory')->willReturn('Digital');

        $item2 = $this->getMock('Teknoo\Paypal\Express\Entity\PurchaseItemInterface');
        $item2->expects($this->any())->method('getPaymentRequestName')->willReturn('name 2');
        $item2->expects($this->any())->method('getPaymentRequestDesc')->willReturn('');
        $item2->expects($this->any())->method('getPaymentRequestAmount')->willReturn(456);
        $item2->expects($this->any())->method('getPaymentRequestQantity')->willReturn(3);
        $item2->expects($this->any())->method('getPaymentRequestNumber')->willReturn('');
        $item2->expects($this->any())->method('getPaymentRequestUrl')->willReturn('');
        $item2->expects($this->any())->method('getPaymentRequestItemCategory')->willReturn('Physical');

        $object = $this->generateObject();
        $this->assertEquals($object, $object->addItem($item1));
        $this->assertEquals($object, $object->addItem($item2));

        $array = $object->toArray();
        $this->assertEquals(
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
            $array->getArrayCopy()
        );
    }
}
