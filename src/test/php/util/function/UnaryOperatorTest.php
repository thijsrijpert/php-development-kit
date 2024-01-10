<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 */
namespace jhp\util\function;

use jhp\lang\exception\IllegalArgumentException;
use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use PHPUnit\Framework\TestCase;
use TypeError;

class UnaryOperatorTest extends TestCase
{

    function testUnaryOperatorSuccess(): void
    {
        $value1 = new TestObject();
        $function = UnaryOperator::of(fn(TestObject $value1) => $value1->setValue("Set"));

        $result = $function->apply($value1);

        $this->assertTrue($value1->isSetterInvoked());
        $this->assertEquals("Set", $value1->getValue());
        $this->assertEquals("Set", $result->getValue());
    }

    function testUnaryOperatorSuccessWithReturnType(): void
    {
        $value1 = new TestObject();
        $function = UnaryOperator::of(fn(TestObject $value1) => $value1->setValue("Set"), TestObject::class);

        $result = $function->apply($value1);

        $this->assertTrue($value1->isSetterInvoked());
        $this->assertEquals("Set", $value1->getValue());
        $this->assertEquals("Set", $result->getValue());
    }

    function testUnaryOperatorSuccessDifferentReturnType(): void
    {
        $this->expectException(TypeError::class);
        $value1 = new TestObject();
        $function = UnaryOperator::of(fn(TestObject $value1) => (new NotTestObject())->setValue("Set"), NotTestObject::class);

        $function->apply($value1);
    }

    function testUnaryOperatorSuccessWithoutReturnType(): void
    {
        $this->expectException(TypeError::class);
        $value1 = new TestObject();
        $function = UnaryOperator::of(fn(TestObject $value1) => (new NotTestObject())->setValue("Set"));

        $function->apply($value1);
    }

    function testUnaryOperatorInvalidReturnType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $function = UnaryOperator::of(fn(TestObject $value1) => (new TestObject())->setValue("Set"), NotTestObject::class);

        $function->apply($value1);
    }

    function testUnaryOperatorInvalidParameterCount(): void
    {
        $this->expectException(IllegalArgumentException::class);
        UnaryOperator::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("Set"));
    }

    function testUnaryOperatorInvalidType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new NotTestObject();
        $consumer = UnaryOperator::of(fn(TestObject $value1) => $value1->setValue("Set"));

        $consumer->apply($value1);
    }
}

