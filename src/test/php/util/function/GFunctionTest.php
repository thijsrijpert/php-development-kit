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

class GFunctionTest extends TestCase
{

    function testGFunctionSuccess(): void
    {
        $value1 = new TestObject();
        $function = GFunction::of(fn(TestObject $value1) => $value1->setValue("ISet"));

        $result = $function->apply($value1);

        $this->assertTrue($value1->isSetterInvoked());
        $this->assertEquals("ISet", $value1->getValue());
        $this->assertEquals("ISet", $result->getValue());
    }

    function testGFunctionSuccessWithReturnType(): void
    {
        $value1 = new TestObject();
        $function = GFunction::of(fn(TestObject $value1) => $value1->setValue("ISet"), TestObject::class);

        $result = $function->apply($value1);

        $this->assertTrue($value1->isSetterInvoked());
        $this->assertEquals("ISet", $value1->getValue());
        $this->assertEquals("ISet", $result->getValue());
    }

    function testGFunctionSuccessDifferentReturnType(): void
    {
        $value1 = new TestObject();
        $function = GFunction::of(fn(TestObject $value1) => (new NotTestObject())->setValue("ISet"), NotTestObject::class);

        $result = $function->apply($value1);

        $this->assertFalse($value1->isSetterInvoked());
        $this->assertEquals("DefaultValue", $value1->getValue());
        $this->assertEquals("ISet", $result->getValue());
    }

    function testGFunctionInvalidReturnType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $function = GFunction::of(fn(TestObject $value1) => (new TestObject())->setValue("ISet"), NotTestObject::class);

        $function->apply($value1);
    }

    function testGFunctionInvalidParameterCount(): void
    {
        $this->expectException(IllegalArgumentException::class);
        GFunction::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("ISet"));
    }

    function testGFunctionInvalidType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new NotTestObject();
        $consumer = GFunction::of(fn(TestObject $value1) => $value1->setValue("ISet"));

        $consumer->apply($value1);
    }
}

