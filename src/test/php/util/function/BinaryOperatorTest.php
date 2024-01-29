<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 *
 * This code is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License version 2 only, as
 * published by the Free Software Foundation.  This particular file is
 * designated as subject to the "Classpath" exception as provided in the
 * LICENSE file that accompanied this code.
 *
 * This code is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
 * version 2 for more details (a copy is included in the LICENSE file that
 * accompanied this code).
 *
 * You should have received a copy of the GNU General Public License version
 * 2 along with this work; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */
namespace jhp\util\function;

use jhp\lang\exception\IllegalArgumentException;
use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use PHPUnit\Framework\TestCase;
use TypeError;

class BinaryOperatorTest extends TestCase
{

    function testBinaryOperatorSuccess(): void
    {
        $value1 = new TestObject();
        $value2 = new TestObject();
        $function = BinaryOperator::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("Set"));

        $result = $function->apply($value1, $value2);

        $this->assertTrue($value1->isSetterInvoked());
        $this->assertFalse($value2->isSetterInvoked());
        $this->assertEquals("Set", $value1->getValue());
        $this->assertEquals("DefaultValue", $value2->getValue());
        $this->assertEquals("Set", $result->getValue());
    }

    function testBinaryOperatorSuccessWithReturnType(): void
    {
        $value1 = new TestObject();
        $value2 = new TestObject();
        $function = BinaryOperator::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("Set"), TestObject::class);

        $result = $function->apply($value1, $value2);

        $this->assertTrue($value1->isSetterInvoked());
        $this->assertFalse($value2->isSetterInvoked());
        $this->assertEquals("Set", $value1->getValue());
        $this->assertEquals("DefaultValue", $value2->getValue());
        $this->assertEquals("Set", $result->getValue());
    }

    function testBinaryOperatorSuccessDifferentReturnType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $value2 = new TestObject();
        $function = BinaryOperator::of(fn(TestObject $value1, TestObject $value2) => (new NotTestObject())->setValue("Set"), NotTestObject::class);

        $function->apply($value1, $value2);
    }


    function testBinaryOperatorSuccessDifferentParameterTypes(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $value2 = new NotTestObject();
        $function = BinaryOperator::of(fn(TestObject $value1, NotTestObject $value2) => (new TestObject())->setValue("Set"), TestObject::class);

        $function->apply($value1, $value2);
    }

    function testBinaryOperatorInvalidReturnType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $value2 = new TestObject();
        $function = BinaryOperator::of(fn(TestObject $value1, TestObject $value2) => (new TestObject())->setValue("Set"), NotTestObject::class);

        $function->apply($value1, $value2);
    }

    function testBinaryOperatorInvalidParameterCount(): void
    {
        $this->expectException(IllegalArgumentException::class);
        BinaryOperator::of(fn(TestObject $value1) => $value1->setValue("Set"));
    }

    function testBinaryOperatorInvalidType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $value2 = new NotTestObject();
        $consumer = BinaryOperator::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("Set"));

        $consumer->apply($value1, $value2);
    }
}

