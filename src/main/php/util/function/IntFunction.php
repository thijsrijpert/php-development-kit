<?php

namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class IntFunction {

    protected const CLOSURE_PARAMETER_COUNT = 1;

    protected function __construct(
        protected readonly ClosureValidationHelper $closureHelper,
        protected readonly string $returnType
    ){ }

    abstract function apply(int $value): object;

    public static function of(Closure $closure,
                              ?string $returnType = null
    ): IntFunction {
        $closureHelper = new ClosureValidationHelper($closure);
        $closureHelper->assertParameterCount(IntFunction::CLOSURE_PARAMETER_COUNT);

        return new class($closureHelper, $returnType) extends IntFunction {
            function apply(int $value): object {
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