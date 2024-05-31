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
namespace jhp\util\collection;

use jhp\lang\exception\IllegalArgumentException;
use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use jhp\util\function\BiConsumer;
use PHPUnit\Framework\TestCase;
use TypeError;

class ComparatorTest extends TestCase
{
    function testComparatorSuccess(): void
    {
        $testObject1 = new TestObject();
        $testObject2 = new TestObject();
        $comparator = Comparator::of(fn(TestObject $value1, TestObject $value2) => $value1->setValue("ISet") <=> $value2);

        $result = $comparator->compare($testObject1, $testObject2);

        $this->assertEquals(1, $result);
    }

    function testComparatorInvalidParameterCount(): void
    {
        $this->expectException(IllegalArgumentException::class);
        Comparator::of(fn(TestObject $value1) => $value1->setValue("ISet"));
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

