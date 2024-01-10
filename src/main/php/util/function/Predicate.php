<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 */
/*
 * Copyright (c) 2010, 2013, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 *
 * This code is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License version 2 only, as
 * published by the Free Software Foundation.  Oracle designates this
 * particular file as subject to the "Classpath" exception as provided
 * by Oracle in the LICENSE file that accompanied this code.
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
 *
 * Please contact Oracle, 500 Oracle Parkway, Redwood Shores, CA 94065 USA
 * or visit www.oracle.com if you need additional information or have any
 * questions.
 */
namespace jhp\util\function;

use Closure;
use jhp\util\function\internal\ClosureValidationHelper;
use jhp\util\function\internal\TypeErrorHelper;
use TypeError;

abstract class Predicate {

    protected const CLOSURE_PARAMETER_COUNT = 1;

    protected function __construct(
        protected readonly ClosureValidationHelper $closureHelper
    ){ }

    abstract function test(object $value): bool;

    public static function of(Closure $closure): Predicate {

        $closureHelper = new ClosureValidationHelper($closure);
        $closureHelper->assertParameterCount(Predicate::CLOSURE_PARAMETER_COUNT);

        return new class($closureHelper) extends Predicate {
            function test($value): bool {
                try {
                    $result = $this->closureHelper->getClosure()->call($this, $value);
                } catch (TypeError $e) {
                    throw TypeErrorHelper::convertToFunctionalTypeError($e);
                }
                return $result;
            }
        };
    }
}