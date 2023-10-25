<?php

namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class BiFunction {

    protected const CLOSURE_PARAMETER_COUNT = 2;

    protected function __construct(
        protected readonly ClosureValidationHelper $closureHelper,
        protected ?string $returnType = null
    ){ }

    abstract function apply(object $value, object $value2): object;

    public static function of(Closure $closure,
                              ?string $returnType = null
    ): BiFunction {

        $helper = new ClosureValidationHelper($closure);
        $helper->assertParameterCount(BiFunction::CLOSURE_PARAMETER_COUNT);

        return new class($helper, $returnType) extends BiFunction {
            function apply(object $value, object $value2): object {
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