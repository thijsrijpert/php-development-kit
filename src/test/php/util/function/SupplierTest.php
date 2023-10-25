<?php

namespace jhp\util\function;

use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use jhp\util\function\internal\IllegalArgumentException;
use PHPUnit\Framework\TestCase;
use TypeError;

class SupplierTest extends TestCase
{

    function testSupplierSuccess(): void {
    
        $function = Supplier::of(fn() => new TestObject());

        $result = $function->get();

        $this->assertTrue($result instanceof TestObject);
        $this->assertEquals("DefaultValue", $result->getValue());
    }

    function testSupplierSuccessWithReturnType(): void
    {
        $function = Supplier::of(fn() => new TestObject(), TestObject::class);

        $result = $function->get();

        $this->assertTrue($result instanceof TestObject);
        $this->assertEquals("DefaultValue", $result->getValue());
    }

    function testSupplierInvalidReturnType(): void
    {
        $this->expectException(TypeError::class);

        $function = Supplier::of(fn() => new TestObject(), NotTestObject::class);

        $result = $function->get();
    }

    function testSupplierInvalidParameterCount(): void
    {
        $this->expectException(IllegalArgumentException::class);
        Supplier::of(fn(TestObject $value1) => true);
    }
}

