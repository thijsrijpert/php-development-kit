<?php

namespace jhp\util\function;

use Closure;
use jhp\lang\TClass;
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
                try {
                    $result =  $this->closureHelper->getClosure()->call($this, $value, $value2);
                } catch (TypeError $e) {
                    throw TypeErrorHelper::convertToFunctionalTypeError($e);
                }

                if ($this->returnType === null) {
                    $this->returnType = TClass::of($result)->getName();
                }

                $this->closureHelper->validateType($result, $this->returnType);
                $this->closureHelper->validateType($value, $this->returnType);
                $this->closureHelper->validateType($value2, $this->returnType);
                return $result;
            }
        };
    }
}