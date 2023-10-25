<?php

namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class Predicate {

    protected const CLOSURE_PARAMETER_COUNT = 1;

    protected function __construct(
        protected readonly ClosureValidationHelper $closureHelper
    ){ }

    abstract function test(object $value): bool;

    public static function of(Closure $closure): Predicate {

        $closureHelper = new ClosureValidationHelper($closure);
        $closureHelper->assertParameterCount(Predicate::CLOSURE_PARAMETER_COUNT);

        return new class($closureHelper) extends Predicate {
            function test($value): bool {
                try {
                    $result = $this->closureHelper->getClosure()->call($this, $value);
                } catch (TypeError $e) {
                    throw TypeErrorHelper::convertToFunctionalTypeError($e);
                }
                return $result;
            }
        };
    }
}