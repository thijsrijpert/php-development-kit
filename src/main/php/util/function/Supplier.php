<?php

namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;

abstract class Supplier {
    protected const CLOSURE_PARAMETER_COUNT = 0;

    protected function __construct(
        protected readonly ClosureValidationHelper $closureHelper,
        protected readonly ?string                 $returnType = null
    ){ }

    abstract function get(): object;

    public static function of(Closure $closure,
                              ?string $returnType = null
    ): Supplier {
        $closureHelper = new ClosureValidationHelper($closure);
        $closureHelper->assertParameterCount(Supplier::CLOSURE_PARAMETER_COUNT);

        return new class($closureHelper, $returnType) extends Supplier {
            function get(): object {
                $result = $this->closureHelper->getClosure()->call($this);
                if ($this->returnType !== null) {
                    $this->closureHelper->validateType($result, $this->returnType);
                }
                return $result;
            }
        };
    }
}