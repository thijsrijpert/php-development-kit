<?php

namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class BinaryOperator extends BiFunction {

    public static function of(Closure $closure,
                              ?string $returnType = null
    ): BinaryOperator {

        $helper = new ClosureValidationHelper($closure);
        $helper->assertParameterCount(BiFunction::CLOSURE_PARAMETER_COUNT);

        return new class($helper, $returnType) extends BinaryOperator {
            function apply($value, $value2): object {
                if ($this->returnType !== null) {
                    $this->closureHelper->validateType($value, $this->returnType);
                    $this->closureHelper->validateType($value2, $this->returnType);
                }

                try {
                    $result =  $this->closureHelper->getClosure()->call($this, $value, $value2);
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