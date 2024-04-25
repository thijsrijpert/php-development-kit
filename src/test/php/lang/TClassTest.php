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

use ArrayAccess;
use jhp\util\collection\ICollection;
use jhp\util\collection\IList;
use PHPUnit\Framework\TestCase;

use function jhp\lang\functional\c;
use function jhp\lang\functional\i;

class TClassTest extends TestCase
{
    public function testEqualsDifferentClass() {
        $this->assertFalse(c(TObject::class)->equals(c(TInteger::class)));
    }

    public function testEqualsNull() {
        $this->assertFalse(c(TObject::class)->equals());
    }


    public function testEqualsSameClass() {
        $this->assertTrue(c(TObject::class)->equals(c(TObject::class)));
    }

    public function testEqualsNotAClass() {
        $this->assertFalse(c(TObject::class)->equals(new TObject()));
    }

    public function testIsAssignableFromSame() {
        $this->assertTrue(c(TObject::class)->isAssignableFrom(c(TObject::class)));
    }

    public function testIsAssignableFromSuper() {
        $this->assertTrue(c(TObject::class)->isAssignableFrom(c(TInteger::class)));
    }

    public function testIsAssignableFromSub() {
        $this->assertFalse(c(TInteger::class)->isAssignableFrom(c(TObject::class)));
    }

    public function testIsAssignableFromSameInterface() {
        $this->assertTrue(c(IObject::class)->isAssignableFrom(c(IObject::class)));
    }

    public function testIsAssignableFromSuperInterface() {
        $this->assertTrue(c(IObject::class)->isAssignableFrom(c(IList::class)));
    }

    public function testIsAssignableFromSubInterface() {
        $this->assertFalse(c(IList::class)->isAssignableFrom(c(IObject::class)));
    }

    public function testIsAssignableFromSuperInterfaceAndClass() {
        $this->assertTrue(c(IObject::class)->isAssignableFrom(c(TClass::class)));
    }

    public function testIsAssignableFromSubInterfaceAndClass() {
        $this->assertFalse(c(TClass::class)->isAssignableFrom(c(IObject::class)));
    }

    public function testIsInstanceFromSame() {
        $this->assertTrue(c(TObject::class)->isInstance(new TObject()));
    }

    public function testIsInstanceFromSuper() {
        $this->assertTrue(c(TObject::class)->isInstance(i(1)));
    }

    public function testIsInstanceFromSub() {
        $this->assertFalse(c(TInteger::class)->isInstance(new TObject));
    }

    public function testIsInstanceFromSuperInterfaceAndClass() {
        $this->assertTrue(c(IObject::class)->isInstance(new TObject()));
    }

    public function testGetInterfaces() {
        $result = c(ICollection::class)->getInterfaces();
        $this->assertTrue($result->contains(c(IIterable::class)));
        $this->assertTrue($result->contains(c(ArrayAccess::class)));
        $this->assertTrue($result->contains(c(IObject::class)));
    }

    public function testGetInterfacesEmpty() {
        $result = c(IObject::class)->getInterfaces();
        $this->assertTrue($result->isEmpty());
    }

    public function testFrom() {
        $this->assertEquals("jhp\lang\TObject", TClass::of(new TObject())->getName());
    }

}