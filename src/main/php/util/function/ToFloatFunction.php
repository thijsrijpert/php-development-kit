<?php

namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class ToFloatFunction {

    protected const CLOSURE_PARAMETER_COUNT = 1;

    protected function __construct(
        protected readonly ClosureValidationHelper $closureHelper
    ){ }

    abstract function applyAsFloat($value): int;

    public static function of(Closure $closure): ToFloatFunction {
        $closureHelper = new ClosureValidationHelper($closure);
        $closureHelper->assertParameterCount(ToFloatFunction::CLOSURE_PARAMETER_COUNT);

        return new class($closureHelper) extends ToFloatFunction {
            function applyAsFloat($value): int {
                try {
                    return $this->closureHelper->getClosure()->call($this, $value);
                } catch (TypeError $e) {
                    throw TypeErrorHelper::convertToFunctionalTypeError($e);
                }
            }
        };
    }
}