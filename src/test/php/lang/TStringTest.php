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

use PHPUnit\Framework\TestCase;

use function jhp\lang\functional\s;

class TStringTest extends TestCase
{
    function testEquals() {
        $this->assertTrue(s("Test")->equals(s("Test")));
    }

    function testEqualsNot() {
        $this->assertFalse(s("Test")->equals(s("NotTest")));
    }

    function testEqualsDifferentType() {
        $this->assertFalse(s("Test")->equals(new TObject()));
    }

    function testEqualsNull() {
        $this->assertFalse(s("Test")->equals());
    }

    function testHashcode() {
        $this->assertNotEquals(s("test")->hashCode(), s("Test")->hashCode());
        $this->assertEquals(s("Test")->hashCode(), s("Test")->hashCode());
        $this->assertNotEquals(0, s("Test")->hashCode());
    }

    function testHashcodeEmptyString() {
        $this->assertEquals(0, s("")->hashCode());
    }

    function testHashcodeVeryLongString() {
        $this->assertEquals(-7850630014781073561, s("1232132132132132132132131236676767676767676734534534534534534")->hashCode());
    }

    function testConcat() {
        $this->assertEquals("TestTest", s("Test") . s("Test"));
    }
}