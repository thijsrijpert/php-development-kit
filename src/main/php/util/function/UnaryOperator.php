<?php

namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class UnaryOperator extends GFunction {

    public static function of(Closure $closure,
                              ?string $returnType = null
    ): UnaryOperator {

        $helper = new ClosureValidationHelper($closure);
        $helper->assertParameterCount(GFunction::CLOSURE_PARAMETER_COUNT);

        return new class($closure, $returnType) extends UnaryOperator {
            function apply($value): object {
                if ($this->returnType !== null) {
                    $this->closureHelper->validateType($value, $this->returnType);
                }

                try {
                    $result =  $this->closureHelper->getClosure()->call($this, $value);
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