<?php

namespace jhp\util\function;

use jhp\lang\exception\IllegalArgumentException;
use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use PHPUnit\Framework\TestCase;
use TypeError;

class BiConsumerTest extends TestCase
{
    function testBiConsumerSuccess(): void
    {
        $value1 = new TestObject();
        $value2 = new TestObject();
        $consumer = BiConsumer::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("Set"));

        $consumer->accept($value1, $value2);

        $this->assertTrue($value1->isSetterInvoked());
        $this->assertFalse($value2->isSetterInvoked());
        $this->assertEquals("Set", $value1->getValue());
        $this->assertEquals("DefaultValue", $value2->getValue());
    }

    function testBiConsumerInvalidParameterCount(): void
    {
        $this->expectException(IllegalArgumentException::class);
        BiConsumer::of(fn(TestObject $value1) => $value1->setValue("Set"));
    }

    function testBiConsumerInvalidType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $value2 = new NotTestObject();
        $consumer = BiConsumer::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("Set"));

        $consumer->accept($value1, $value2);
    }
}

