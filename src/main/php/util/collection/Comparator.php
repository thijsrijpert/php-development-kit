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
/*
 * Copyright (c) 1997, 2021, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */

namespace jhp\util\collection;

use Closure;
use jhp\lang\IObject;
use jhp\lang\TClass;
use jhp\util\function\FunctionalInterface;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class Comparator implements FunctionalInterface {

    private const CLOSURE_PARAMETER_COUNT = 2;

    private function __construct(
        protected readonly Closure $closure,
        protected readonly ClosureValidationHelper $closureHelper
    ){ }

    abstract function compare(IObject $value, IObject $value2): int;

    public function getClosure(): Closure
    {
        return $this->closure;
    }

    public static function of(Closure $closure): Comparator
    {
        $helper = new ClosureValidationHelper($closure);
        $helper->assertParameterCount(Comparator::CLOSURE_PARAMETER_COUNT);

        return new class($closure, $helper) extends Comparator {
            function compare($value, $value2): int {
                $this->closureHelper->validateType($value, TClass::of($value2)->getName());
                $this->closureHelper->validateType($value2, TClass::of($value)->getName());

                try {
                    return $this->closureHelper->getClosure()->call($this, $value, $value2);
                } catch (TypeError $e) {
                    throw TypeErrorHelper::convertToFunctionalTypeError($e);
                }
            }
        };
    }
}