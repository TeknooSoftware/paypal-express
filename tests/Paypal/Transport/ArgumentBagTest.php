<?php

namespace UniAlteri\Tests\Paypal\Transport;

use UniAlteri\Paypal\Express\Transport\ArgumentBag;

class ArgumentBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Generate testable object
     * @return ArgumentBag
     */
    protected function generateObject()
    {
        return new ArgumentBag();
    }

    /**
     * @covers UniAlteri\Paypal\Express\Transport\ArgumentBag::reset()
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
     * @covers UniAlteri\Paypal\Express\Transport\ArgumentBag::set()
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
     * @covers UniAlteri\Paypal\Express\Transport\ArgumentBag::get()
     */
    public function testGet()
    {
        $object = $this->generateObject();
        $object->set('foo', 'bar');
        $this->assertEquals('bar', $object->get('foo'));
    }

    /**
     * @covers UniAlteri\Paypal\Express\Transport\ArgumentBag::toArray()
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