<?php

namespace jhp\util\collection;

use jhp\lang\Clazz;
use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use TypeError;

class ArrayListTest extends TestCase
{
    private array $data;

    function setUp() {
        $this->data = [
            (new TestObject())->setValue("One"),
            (new TestObject())->setValue("Two"),
            (new TestObject())->setValue("Three"),
            (new TestObject())->setValue("Four"),
            (new TestObject())->setValue("Five"),
        ];
    }

    function testInitInvalidDataType() {
        $this->assertThrows(TypeError::class);
        $this->data[5] = (new NotTestObject())->setValue("Six");
        new ArrayList(Clazz::from(TestObject::class), $this->data);
    }

    function testInitNotAnObject() {
        $this->assertThrows(TypeError::class);
        $this->data[5] = 6;
        new ArrayList(Clazz::from(TestObject::class), $this->data);
    }

    function testSize() {
        $list = new ArrayList(Clazz::from(TestObject::class), $this->data);

        $result = $list->size();

        $this->assertEquals(5, $result);
    }

    function testIsEmptyFilled() {
        $list = new ArrayList(Clazz::from(TestObject::class), $this->data);

        $result = $list->isEmpty();

        $this->assertFalse($result);
    }

    function testIsEmptyEmpty() {
        $list = new ArrayList(Clazz::from(TestObject::class));

        $result = $list->isEmpty();

        $this->assertTrue($result);
    }

    function testContainsTrue() {
        $list = new ArrayList(Clazz::from(TestObject::class), $this->data);

        $result = $list->contains((new TestObject())->setValue("Three"));

        $this->assertTrue($result);
    }

    function testContainsFalse() {
        $list = new ArrayList(Clazz::from(TestObject::class), $this->data);

        $result = $list->contains((new TestObject())->setValue("Zero"));

        $this->assertFalse($result);
    }

    function testAddAndGet() {
        $input = (new TestObject())->setValue("Six");
        $list = new ArrayList(Clazz::from(TestObject::class), $this->data);

        $result = $list->add($input);
        $addedValue = $list->get(5);

        $this->assertTrue($result);
        $this->assertEquals($input, $addedValue);
    }

    function testAddInvalidType() {
        $this->assertThrows(TypeError::class);
        $input = (new NotTestObject())->setValue("Six");
        $list = new ArrayList(Clazz::from(TestObject::class), $this->data);

        $list->add($input);
    }

    function testAddAndGetIndexed() {
        $input = (new TestObject())->setValue("Six");
        $list = new ArrayList(Clazz::from(TestObject::class), $this->data);

        $result = $list->add(3, $input);
        $addedValue = $list->get(3);

        $this->assertTrue($result);
        $this->assertEquals($input, $addedValue);
    }

    function testAddInvalidIndexed() {
        $this->assertThrows(TypeError::class);
        $input = (new NotTestObject())->setValue("Six");
        $list = new ArrayList(Clazz::from(TestObject::class), $this->data);

        $list->add(3, $input);
    }

    function testContainsAllTrue() {
        $list = new ArrayList(Clazz::from(TestObject::class), $this->data);

        $result = $list->contains(new ArrayList(Clazz::from(TestObject::class), [
            (new TestObject())->setValue("Three"),
            (new TestObject())->setValue("Two")]
        ));

        $this->assertTrue($result);
    }

    function testContainsAllFalse() {
        $list = new ArrayList(Clazz::from(TestObject::class), $this->data);

        $result = $list->contains(new ArrayList(Clazz::from(TestObject::class), [
                (new TestObject())->setValue("Zero"),
                (new TestObject())->setValue("Two")]
        ));

        $this->assertFalse($result);
    }
}