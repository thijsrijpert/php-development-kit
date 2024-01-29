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

