<?php

namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class GFunction {

    protected const CLOSURE_PARAMETER_COUNT = 1;

    protected function __construct(
        protected readonly ClosureValidationHelper $closureHelper,
        protected ?string $returnType = null
    ){ }

    abstract function apply(object $value): object;

    public static function of(Closure $closure,
                              ?string $returnType = null
    ): GFunction {
        $closureHelper = new ClosureValidationHelper($closure);
        $closureHelper->assertParameterCount(GFunction::CLOSURE_PARAMETER_COUNT);

        return new class($closureHelper, $returnType) extends GFunction {
            function apply($value): object {
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