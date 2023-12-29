<?php

namespace jhp\util\function;

use Closure;
use jhp\lang\TClass;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class UnaryOperator extends GFunction {

    public static function of(Closure $closure,
                              ?string $returnType = null
    ): UnaryOperator {

        $helper = new ClosureValidationHelper($closure);
        $helper->assertParameterCount(GFunction::CLOSURE_PARAMETER_COUNT);

        return new class($helper, $returnType) extends UnaryOperator {
            function apply(object $value): object {
                try {
                    $result =  $this->closureHelper->getClosure()->call($this, $value);
                } catch (TypeError $e) {
                    throw TypeErrorHelper::convertToFunctionalTypeError($e);
                }

                if ($this->returnType === null) {
                    $this->returnType = TClass::of($result)->getName();
                }

                $this->closureHelper->validateType($value, $this->returnType);
                $this->closureHelper->validateType($result, $this->returnType);
                return $result;
            }
        };
    }
}