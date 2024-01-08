<?php

namespace jhp\testhelper;

use jhp\lang\Comparable;
use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\IObject;

class SortableTestObject extends TestObject implements Comparable
{

    public function compareTo(IObject $o): int
    {
        if ($o instanceof SortableTestObject) {
            return $this->getValue() <=> $o->getValue();
        }

        throw new IllegalArgumentException("");
    }
}