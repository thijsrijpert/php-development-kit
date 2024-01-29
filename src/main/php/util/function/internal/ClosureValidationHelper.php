<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 *
 * This code is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License version 2 only, as
 * published by the Free Software Foundation.  This particular file is
 * designated as subject to the "Classpath" exception as provided in the
 * LICENSE file that accompanied this code.
 *
 * This code is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
 * version 2 for more details (a copy is included in the LICENSE file that
 * accompanied this code).
 *
 * You should have received a copy of the GNU General Public License version
 * 2 along with this work; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace jhp\util\function\internal;

use Closure;
use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\internal\GType;
use jhp\lang\TClass;
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
            return TClass::of($value)->getName() === TClass::from($type)->getName();
        }

        $expected = GType::from($type);

        if ($actual === $expected) {
            return true;
        }

        return false;
    }
}