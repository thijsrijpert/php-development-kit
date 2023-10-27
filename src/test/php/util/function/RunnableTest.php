<?php

namespace jhp\util\function;

use PHPUnit\Framework\TestCase;
use jhp\testhelper\TestObject;

class RunnableTest extends TestCase
{

    function testRunnableSuccess(): void {

        $testobject = new TestObject();
        $function = Runnable::of(fn() => $testobject->setValue("Set"));

        $function->run();

        $this->assertTrue($testobject->isSetterInvoked());
        $this->assertEquals("Set", $testobject->getValue());
    }
}

