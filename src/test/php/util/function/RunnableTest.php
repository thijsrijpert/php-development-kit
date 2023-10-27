<?php

namespace jhp\util\function;

use jhp\testhelper\TestObject;

class RunnableTest extends TestCase
{

    function testSupplierSuccess(): void {

        $testobject = new TestObject();
        $function = Runnable::of(fn() => $testobject->setValue("Set"));

        $function->run();

        $this->assertTrue("DefaultValue", $testobject->isSetterInvoked());
        $this->assertEquals("Set", $testobject->getValue());
    }
}

