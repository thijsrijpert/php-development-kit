<?php

namespace jhp\util;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;

abstract class Comparator {

    private const CLOSURE_PARAMETER_COUNT = 2;

    private function __construct(
        protected readonly Closure $closure
    ){ }

    abstract function compare($value, $value2): int;

    public static function of(Closure $closure) {
        $helper = new ClosureValidationHelper($closure);
        $helper->assertParameterCount(Comparator::CLOSURE_PARAMETER_COUNT);

        return new class($closure) extends Comparator {
            function compare($value, $value2): int {
                return $this->closure->call($this, $value);
            }
        };
    }
}