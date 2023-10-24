<?php

namespace jhp\util\function\internal;

use Closure;
use jhp\lang\Clazz;
use jhp\lang\GType;
use ReflectionException;
use ReflectionFunction;
use TypeError;

class ClosureValidationHelper
{
    public function __construct(
        private readonly Closure $closure
    ) { }

    public function getClosure(): Closure {
        return $this->closure;
    }

    public function assertParameterCount(int $count): void {
        try {
            $parameters = (new ReflectionFunction($this->closure))->getParameters();
            if (count($parameters) != $count) {
                throw new IllegalArgumentException();
            }
        } catch (ReflectionException $e) {
            throw new IllegalArgumentException("Failed to assert the parameter count: " . $e->getMessage());
        }
    }

    public function validateType(mixed $value, string $valueType): void {
        if (!$this->isValidType($value, $valueType)) {
            throw new TypeError("Passed argument is not of the valid datatype");
        }
    }

    private function isValidType(mixed $value, string $type): bool {
        $actual = GType::of($value);
        if ($actual->isObject()) {
            return Clazz::of($value)->getName() === Clazz::from($type)->getName();
        }

        $expected = GType::from($type);

        if ($actual === $expected) {
            return true;
        }

        return false;
    }
}