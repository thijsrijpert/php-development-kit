<?php

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