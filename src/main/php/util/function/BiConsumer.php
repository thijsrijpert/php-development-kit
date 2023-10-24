<?php

namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class BiConsumer {

    protected const CLOSURE_PARAMETER_COUNT = 2;

    protected function __construct(
        protected readonly ClosureValidationHelper $closureHelper
    ){ }

    abstract function accept($value, $value2): void;

    public static function of(Closure $closure): BiConsumer {
        $helper = new ClosureValidationHelper($closure);
        $helper->assertParameterCount(BiConsumer::CLOSURE_PARAMETER_COUNT);

        return new class($helper) extends BiConsumer {
            function accept($value, $value2): void {
                try {
                    $this->closureHelper->getClosure()->call($this, $value, $value2);
                } catch (TypeError $e) {
                    throw TypeErrorHelper::convertToFunctionalTypeError($e);
                }
            }
        };
    }
}