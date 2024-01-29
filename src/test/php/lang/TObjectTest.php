<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 */
namespace jhp\lang;

use jhp\lang\exception\CloneNotSupportedException;
use jhp\lang\exception\IllegalArgumentException;
use jhp\testhelper\TestObject;
use PHPUnit\Framework\TestCase;

class TObjectTest extends TestCase
{
    public function testGetClass() {
        $this->assertEquals(TObject::class, (new TObject())->getClass()->getName());
    }

    public function testGetClassInheritted() {
        $this->assertEquals(TestObject::class, (new TestObject())->getClass()->getName());
    }

    public function testHashcode() {
        $testObject = new TObject();
        $testObject2 = new TObject();
        $testObject3 = $testObject2;

        $this->assertNotEquals($testObject->hashCode(), $testObject2->hashCode());
        $this->assertEquals($testObject2->hashCode(), $testObject3->hashCode());
    }

    public function testEquals() {
        $testObject = new TObject();
        $testObject2 = new TObject();
        $testObject3 = $testObject2;

        $this->assertFalse($testObject->equals($testObject2));
        $this->assertFalse($testObject->equals());
        $this->assertTrue($testObject2->equals($testObject3));
    }

    public function testToString() {
        $testObject = new TObject();
        $testObject2 = new TObject();
        $testObject3 = $testObject2;
        $actualTestObject = new TestObject();

        $this->assertNotEquals($testObject->toString(), $testObject2->toString());
        $this->assertEquals($testObject2->toString(), $testObject3->toString());
        $this->assertStringContainsString("@", $actualTestObject->toString());
        $this->assertStringContainsString("jhp\\testhelper\\TestObject", $actualTestObject->toString());
    }

    public function testClone() {
        $this->expectException(CloneNotSupportedException::class);
        (new TObject())->clone();
    }

    public function testWaitNegativeTimemout() {
        $this->expectException(IllegalArgumentException::class);
        (new TObject())->wait(-1);
    }

    public function testWaitNegativeNano() {
        $this->expectException(IllegalArgumentException::class);
        (new TObject())->wait(0, -1);
    }

    public function testWaitNanoTooLarge() {
        $this->expectException(IllegalArgumentException::class);
        (new TObject())->wait(0, 1000000);
    }
}