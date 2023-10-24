<?php

namespace util\function;

use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use jhp\util\function\Consumer;
use jhp\util\function\internal\IllegalArgumentException;
use PHPUnit\Framework\TestCase;
use TypeError;

class ConsumerTest extends TestCase
{
    function testConsumerSuccess(): void
    {
        $value1 = new TestObject();
        $value2 = new TestObject();
        $consumer = Consumer::of(fn(TestObject $value1) => $value1->setValue("Set"));

        $consumer->accept($value1, $value2);

        $this->assertTrue($value1->isSetterInvoked());
        $this->assertEquals("Set", $value1->getValue());
    }

    function testConsumerInvalidParameterCount(): void
    {
        $this->expectException(IllegalArgumentException::class);
        Consumer::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("Set"));
    }

    function testConsumerInvalidType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new NotTestObject();
        $consumer = Consumer::of(fn(TestObject $value1) => $value1->setValue("Set"));

        $consumer->accept($value1);
    }
}

