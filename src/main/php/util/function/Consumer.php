<?php

namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class Consumer {

    protected const CLOSURE_PARAMETER_COUNT = 1;

    protected function __construct(
        protected readonly ClosureValidationHelper $closureHelper
    ){ }

    abstract function accept($value): void;

    public static function of(Closure $closure): Consumer {
        $closureHelper = new ClosureValidationHelper($closure);
        $closureHelper->assertParameterCount(Consumer::CLOSURE_PARAMETER_COUNT);

        return new class($closureHelper) extends Consumer {
            function accept($value): void {
                try {
                    $this->closureHelper->getClosure()->call($this, $value);
                } catch (TypeError $e) {
                    throw TypeErrorHelper::convertToFunctionalTypeError($e);
                }
            }
        };
    }
}