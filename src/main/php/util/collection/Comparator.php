<?php

namespace jhp\util\collection;

use Closure;
use jhp\util\function\FunctionalInterface;
use jhp\util\function\internal\ClosureValidationHelper;

abstract class Comparator implements FunctionalInterface {

    private const CLOSURE_PARAMETER_COUNT = 2;

    private function __construct(
        protected readonly Closure $closure
    ){ }

    abstract function compare($value, $value2): int;

    public function getClosure(): Closure
    {
        return $this->closure;
    }

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