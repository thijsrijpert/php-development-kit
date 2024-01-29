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

use jhp\lang\exception\CloneNotSupportedException;
use jhp\lang\exception\IllegalArgumentException;
use jhp\testhelper\TestEnum;
use jhp\testhelper\TestObject;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function testName() {
        $this->assertEquals("VALUE_ZERO", TestEnum::VALUE_ZERO->name());
        $this->assertEquals("VALUE_ONE", TestEnum::VALUE_ONE->name());
        $this->assertEquals("VALUE_TWO", TestEnum::VALUE_TWO->name());
    }

    public function testOrdinal() {
        $this->assertEquals(0, TestEnum::VALUE_ZERO->ordinal());
        $this->assertEquals(1, TestEnum::VALUE_ONE->ordinal());
        $this->assertEquals(2, TestEnum::VALUE_TWO->ordinal());
    }

    public function testToString() {
        $this->assertEquals("VALUE_ZERO", TestEnum::VALUE_ZERO->toString());
        $this->assertEquals("VALUE_ONE", TestEnum::VALUE_ONE->toString());
        $this->assertEquals("VALUE_TWO", TestEnum::VALUE_TWO->toString());
    }

    public function testEqualsSame() {
        $this->assertTrue(TestEnum::VALUE_TWO->equals(TestEnum::VALUE_TWO));
    }

    public function testEqualsOther() {
        $this->assertFalse(TestEnum::VALUE_TWO->equals(TestEnum::VALUE_ZERO));
    }

    public function testEqualsNull() {
        $this->assertFalse(TestEnum::VALUE_TWO->equals());
    }

    public function testGetClass() {
        $this->assertEquals(TestEnum::class, TestEnum::VALUE_ZERO->getClass()->getName());
    }

    public function testGetDeclaringClass() {
        $this->assertEquals(TestEnum::class, TestEnum::VALUE_ZERO->getDeclaringClass()->getName());
    }

    public function testHashcodeNotEqual() {
        $this->assertNotEquals(TestEnum::VALUE_ZERO->hashCode(), TestEnum::VALUE_ONE->hashCode());
    }

    public function testHashcodeEqual() {
        $this->assertEquals(TestEnum::VALUE_ZERO->hashCode(), TestEnum::VALUE_ZERO->hashCode());
    }

    public function testCompareToBigger() {
        $this->assertEquals(1, TestEnum::VALUE_ONE->compareTo(TestEnum::VALUE_ZERO));
    }

    public function testCompareToSmaller() {
        $this->assertEquals(-1, TestEnum::VALUE_ONE->compareTo(TestEnum::VALUE_TWO));
    }

    public function testCompareToEqual() {
        $this->assertEquals(0, TestEnum::VALUE_ONE->compareTo(TestEnum::VALUE_ONE));
    }

    public function testCompareToWrongType() {
        $this->expectException(IllegalArgumentException::class);
        TestEnum::VALUE_ONE->compareTo(new TestObject());
    }


    public function testClone() {
        $this->expectException(CloneNotSupportedException::class);
        TestEnum::VALUE_ONE->clone();
    }

    public function testValueOfNotAnEnum() {
        $this->expectException(IllegalArgumentException::class);
        TestEnum::valueOf(TClass::from(TObject::class), "TEST");
    }

    public function testValueOfNotAValue() {
        $this->expectException(IllegalArgumentException::class);
        TestEnum::valueOf(TClass::from(TestEnum::class), "TEST");
    }

    public function testValueOfCorrect() {
        $result = TestEnum::valueOf(TClass::from(TestEnum::class), "VALUE_ONE");
        $this->assertEquals(TestEnum::VALUE_ONE, $result);
    }

    public function testWaitNegativeTimemout() {
        $this->expectException(IllegalArgumentException::class);
        TestEnum::VALUE_ZERO->wait(-1);
    }

    public function testWaitNegativeNano() {
        $this->expectException(IllegalArgumentException::class);
        TestEnum::VALUE_ZERO->wait(0, -1);
    }

    public function testWaitNanoTooLarge() {
        $this->expectException(IllegalArgumentException::class);
        TestEnum::VALUE_ZERO->wait(0, 1000000);
    }
}
