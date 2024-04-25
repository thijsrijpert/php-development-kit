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
namespace jhp\lang;

use jhp\lang\exception\IllegalArgumentException;
use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use jhp\util\collection\Comparator;
use jhp\util\function\BinaryOperator;
use PHPUnit\Framework\TestCase;
use TypeError;

class ComparatorTest extends TestCase
{

    function testComparatorSuccess(): void
    {
        $value1 = new TestObject();
        $value2 = new TestObject();
        $comparator = Comparator::of(fn(TestObject $value1, TestObject $value2) => -1);

        $result = $comparator->compare($value1, $value2);

        $this->assertEquals(-1, $result);
    }


    function testComparatorDifferentParameterTypes(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $value2 = new NotTestObject();
        $comparator = Comparator::of(fn(TestObject $value1, NotTestObject $value2) => -1);

        $comparator->compare($value1, $value2);
    }

    function testComparatorInvalidParameterCount(): void
    {
        $this->expectException(IllegalArgumentException::class);
        Comparator::of(fn(TestObject $value1) => -1);
    }

    function testComparatorInvalidType(): void
    {
        $this->expectException(TypeError::class);

        $value1 = new TestObject();
        $value2 = new NotTestObject();
        $comparator = Comparator::of(fn(TestObject $value1, TestObject $value2) => -1);

        $comparator->compare($value1, $value2);
    }
}

