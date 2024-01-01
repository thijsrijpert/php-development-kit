<?php

namespace jhp\util\function;

use jhp\lang\exception\IllegalArgumentException;
use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use PHPUnit\Framework\TestCase;
use TypeError;

class GFunctionTest extends TestCase
{

    function testGFunctionSuccess(): void
    {
        $value1 = new TestObject();
        $function = GFunction::of(fn(TestObject $value1) => $value1->setValue("Set"));

        $result = $function->apply($value1);

        $this->assertTrue($value1->isSetterInvoked());
        $this->assertEquals("Set", $value1->getValue());
        $this->assertEquals("Set", $result->getValue());
    }

    function testGFunctionSuccessWithReturnType(): void
    {
        $value1 = new TestObject();
        $function = GFunction::of(fn(TestObject $value1) => $value1->setValue("Set"), TestObject::class);

        $result = $function->apply($value1);

        $this->assertTrue($value1->isSetterInvoked());
        $this->assertEquals("Set", $value1->getValue());
        $this->assertEquals("Set", $result->getValue());
    }

    function testGFunctionSuccessDifferentReturnType(): void
    {
        $value1 = new TestObject();
        $function = GFunction::of(fn(TestObject $value1) => (new NotTestObject())->setValue("Set"), NotTestObject::class);

        $result = $function->apply($value1);

        $this->assertFalse($value1->isSetterInvoked());
        $this->assertEquals("DefaultValue", $value1->getValue());
        $this->assertEquals("Set", $result->getValue());
    }

    function testGFunctionInvalidReturnType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $function = GFunction::of(fn(TestObject $value1) => (new TestObject())->setValue("Set"), NotTestObject::class);

        $function->apply($value1);
    }

    function testGFunctionInvalidParameterCount(): void
    {
        $this->expectException(IllegalArgumentException::class);
        GFunction::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("Set"));
    }

    function testGFunctionInvalidType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new NotTestObject();
        $consumer = GFunction::of(fn(TestObject $value1) => $value1->setValue("Set"));

        $consumer->apply($value1);
    }
}

