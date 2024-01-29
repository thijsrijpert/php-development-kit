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
namespace jhp\util;

use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use jhp\util\function\Consumer;
use jhp\util\function\Runnable;
use PHPUnit\Framework\TestCase;
use TypeError;

class OptionalTest extends TestCase
{
    public function testEmpty() {
        $optional = Optional::empty();

        $this->assertTrue($optional->isEmpty());
        $this->assertFalse($optional->isPresent());
    }

    public function testOfNullable() {
        $optional = Optional::ofNullable();

        $this->assertTrue($optional->isEmpty());
        $this->assertFalse($optional->isPresent());
    }

    public function testTryToRetrieveEmpty() {
        $this->expectException(NoSuchElementException::class);

        Optional::empty()->get();
    }

    public function testTryToRetrieveEmptyOrElse() {
        $result = Optional::empty()->orElse((new TestObject())->setValue("TestO"));

        $this->assertEquals("TestO", $result->getValue());
    }

    public function testNotEmpty() {
        $optional = Optional::of((new TestObject())->setValue("TestO"));

        $this->assertFalse($optional->isEmpty());
        $this->assertTrue($optional->isPresent());
        $result = $optional->get();
        if (!($result instanceof TestObject)) {
            $this->fail();
        }
        $this->assertEquals("TestO", $optional->get()->getValue());
    }

    public function testIfPresent() {
        $optional = Optional::of((new TestObject())->setValue("TestO"));

        $optional->ifPresent(Consumer::of(fn(TestObject $o) => $o->setValue("Tester")));

        $this->assertFalse($optional->isEmpty());
        $this->assertTrue($optional->isPresent());
        $result = $optional->get();
        if (!($result instanceof TestObject)) {
            $this->fail();
        }
        $this->assertEquals("Tester", $result->getValue());
    }

    public function testIfPresentWrongType() {
        $this->expectException(TypeError::class);
        $optional = Optional::of((new TestObject())->setValue("TestO"));

        $optional->ifPresent(Consumer::of(fn(NotTestObject $o) => $o->setValue("Tester")));
    }

    public function testIfPresentOrElsePresent() {
        $runableTiggered = false;
        $optional = Optional::of((new TestObject())->setValue("TestO"));

        $optional->ifPresentOrElse(Consumer::of(fn(TestObject $o) => $o->setValue("Tester")), Runnable::of(fn() => $runableTiggered = true));

        $this->assertFalse($runableTiggered);
        $result = $optional->get();
        if (!($result instanceof TestObject)) {
            $this->fail();
        }
        $this->assertEquals("Tester", $result->getValue());
    }

    public function testIfPresentOrElseWrongType() {
        $this->expectException(TypeError::class);
        $runableTiggered = false;
        $optional = Optional::of((new TestObject())->setValue("TestO"));

        $optional->ifPresentOrElse(Consumer::of(fn(NotTestObject $o) => $o->setValue("Tester")), Runnable::of(fn() => $runableTiggered = true));
    }

    public function testIfPresentOrElseEmpty() {
        $runableTiggered = false;
        $optional = Optional::empty();

        $optional->ifPresentOrElse(Consumer::of(fn(NotTestObject $o) => $o->setValue("Tester")), Runnable::of(function() use (&$runableTiggered) {
            $runableTiggered = true;
        }));

        $this->assertTrue($runableTiggered);
        $this->assertTrue($optional->isEmpty());
    }
}