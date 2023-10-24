<?php

namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class Predicate {

    protected const CLOSURE_PARAMETER_COUNT = 1;

    protected function __construct(
        protected readonly ClosureValidationHelper $closureHelper,
        protected readonly ?string                 $returnType = null
    ){ }

    abstract function test($value): bool;

    public static function of(Closure $closure,
                              ?string $returnType = null
    ): Predicate {
        $closureHelper = new ClosureValidationHelper($closure);
        $closureHelper->assertParameterCount(Predicate::CLOSURE_PARAMETER_COUNT);

        return new class($closureHelper, $returnType) extends Predicate {
            function test($value): bool {
                try {
                    $result = $this->closureHelper->getClosure()->call($this, $value);
                } catch (TypeError $e) {
                    throw TypeErrorHelper::convertToFunctionalTypeError($e);
                }

                if ($this->returnType !== null) {
                    $this->closureHelper->validateType($result, $this->returnType);
                }
                return $result;
            }
        };
    }
}