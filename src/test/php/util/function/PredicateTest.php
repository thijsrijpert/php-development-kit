<?php

namespace jhp\util\function;

use jhp\lang\exception\IllegalArgumentException;
use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use PHPUnit\Framework\TestCase;
use TypeError;

class PredicateTest extends TestCase
{
    function testPredicateSuccessResultTrue(): void
    {
        $object = new TestObject();
        $function = Predicate::of(fn(TestObject $value1) => $value1->getValue() === "DefaultValue");

        $result = $function->test($object);

        $this->assertTrue($result);
    }

    function testPredicateSuccessResultFalse(): void
    {
        $object = new TestObject();
        $function = Predicate::of(fn(TestObject $value1) => $value1->getValue() === "Set");

        $result = $function->test($object);

        $this->assertFalse($result);
    }

    function testPredicateInvalidReturnType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $function = Predicate::of(fn(TestObject $value1) => (new TestObject())->setValue("Set"));

        $function->test($value1);
    }

    function testPredicateInvalidParameterCount(): void
    {
        $this->expectException(IllegalArgumentException::class);
        Predicate::of(fn(TestObject $value1, TestObject $value2) => true);
    }

    function testPredicateInvalidType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new NotTestObject();
        $consumer = Predicate::of(fn(TestObject $value1) => $value1->setValue("Set"));

        $consumer->test($value1);
    }
}