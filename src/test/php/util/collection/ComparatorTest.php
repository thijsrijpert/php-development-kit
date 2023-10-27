<?php

namespace jhp\util\collection;

use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use jhp\util\function\BiConsumer;
use jhp\util\function\internal\IllegalArgumentException;
use PHPUnit\Framework\TestCase;
use TypeError;

class ComparatorTest extends TestCase
{
    function testComparatorSuccess(): void
    {
        $testObject1 = new TestObject();
        $testObject2 = new TestObject();
        $comparator = Comparator::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("Set") <=> $value2);

        $result = $comparator->compare($testObject1, $testObject2);

        $this->assertEquals(1, $result);
    }

    function testComparatorInvalidParameterCount(): void
    {
        $this->expectException(IllegalArgumentException::class);
        Comparator::of(fn(TestObject $value1) => $value1->setValue("Set"));
    }

    function testComparatorInvalidType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $value2 = new NotTestObject();
        $consumer = BiConsumer::of(fn(TestObject $value1, TestObject $value2) => 1);

        $consumer->accept($value1, $value2);
    }

    function testComparatorInvalidReturnType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $value2 = new NotTestObject();
        $consumer = BiConsumer::of(fn(TestObject $value1, TestObject $value2) => new TestObject());

        $consumer->accept($value1, $value2);
    }
}

