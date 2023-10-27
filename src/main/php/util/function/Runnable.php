<?php

namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;

abstract class Runnable {

    protected const CLOSURE_PARAMETER_COUNT = 0;

    protected function __construct(
        protected readonly ClosureValidationHelper $closureHelper
    ){ }

    abstract function run(): void;

    public static function of(Closure $closure): Runnable {
        $closureHelper = new ClosureValidationHelper($closure);
        $closureHelper->assertParameterCount(Runnable::CLOSURE_PARAMETER_COUNT);

        return new class($closureHelper) extends Runnable {
            function run(): void {
                $this->closureHelper->getClosure()->call($this);
            }
        };
    }
}