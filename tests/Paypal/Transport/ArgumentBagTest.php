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
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @license     http://teknoo.software/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     0.8.3
 */

namespace Teknoo\tests\Paypal\Transport;

use Teknoo\Paypal\Express\Transport\ArgumentBag;

/**
 * Class ArgumentBagTest.
 *
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @license     http://teknoo.software/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
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
}
