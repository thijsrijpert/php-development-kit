<?php

namespace jhp\util\function;

use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use jhp\util\function\BiConsumer;
use jhp\util\function\BiFunction;
use jhp\util\function\internal\IllegalArgumentException;
use PHPUnit\Framework\TestCase;
use TypeError;

class BiFunctionTest extends TestCase
{

    function testBiFunctionSuccess(): void
    {
        $value1 = new TestObject();
        $value2 = new TestObject();
        $function = BiFunction::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("Set"));

        $result = $function->apply($value1, $value2);

        $this->assertTrue($value1->isSetterInvoked());
        $this->assertFalse($value2->isSetterInvoked());
        $this->assertEquals("Set", $value1->getValue());
        $this->assertEquals("DefaultValue", $value2->getValue());
        $this->assertEquals("Set", $result->getValue());
    }

    function testBiFunctionSuccessWithReturnType(): void
    {
        $value1 = new TestObject();
        $value2 = new TestObject();
        $function = BiFunction::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("Set"), TestObject::class);

        $result = $function->apply($value1, $value2);

        $this->assertTrue($value1->isSetterInvoked());
        $this->assertFalse($value2->isSetterInvoked());
        $this->assertEquals("Set", $value1->getValue());
        $this->assertEquals("DefaultValue", $value2->getValue());
        $this->assertEquals("Set", $result->getValue());
    }

    function testBiFunctionSuccessDifferentReturnType(): void
    {
        $value1 = new TestObject();
        $value2 = new TestObject();
        $function = BiFunction::of(fn(TestObject $value1, TestObject $value2) => (new NotTestObject())->setValue("Set"), NotTestObject::class);

        $result = $function->apply($value1, $value2);

        $this->assertFalse($value1->isSetterInvoked());
        $this->assertFalse($value2->isSetterInvoked());
        $this->assertEquals("DefaultValue", $value1->getValue());
        $this->assertEquals("DefaultValue", $value2->getValue());
        $this->assertEquals("Set", $result->getValue());
    }

    function testBiFunctionInvalidReturnType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $value2 = new TestObject();
        $function = BiFunction::of(fn(TestObject $value1, TestObject $value2) => (new TestObject())->setValue("Set"), NotTestObject::class);

        $function->apply($value1, $value2);
    }

    function testBiFunctionInvalidParameterCount(): void
    {
        $this->expectException(IllegalArgumentException::class);
        BiFunction::of(fn(TestObject $value1) => $value1->setValue("Set"));
    }

    function testBiFunctionInvalidType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $value2 = new NotTestObject();
        $consumer = BiFunction::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("Set"));

        $consumer->apply($value1, $value2);
    }
}

