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