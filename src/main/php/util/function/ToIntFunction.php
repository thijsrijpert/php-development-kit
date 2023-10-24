<?php

namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class ToIntFunction {

    protected const CLOSURE_PARAMETER_COUNT = 1;

    protected function __construct(
        protected readonly ClosureValidationHelper $closureHelper
    ){ }

    abstract function applyAsInt($value): int;

    public static function of(Closure $closure): ToIntFunction {
        $closureHelper = new ClosureValidationHelper($closure);
        $closureHelper->assertParameterCount(ToIntFunction::CLOSURE_PARAMETER_COUNT);

        return new class($closureHelper) extends ToIntFunction {
            function applyAsInt($value): int {
                try {
                    return $this->closureHelper->getClosure()->call($this, $value);
                } catch (TypeError $e) {
                    throw TypeErrorHelper::convertToFunctionalTypeError($e);
                }
            }
        };
    }
}