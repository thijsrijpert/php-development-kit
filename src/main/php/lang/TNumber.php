<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 */
/*
 * Copyright (c) 1994, 2021, Oracle and/or its affiliates. All rights reserved.
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

namespace jhp\lang;

use jhp\io\Serializable;
use jhp\lang\exception\UnsupportedOperationException;

/**
 * The abstract class Number is the superclass of platform
 * classes representing numeric values that are convertible to the
 * primitive types byte, double, float, int, long, and short.
 *
 * The specific semantics of the conversion from the numeric value of
 * a particular Number implementation to a given primitive
 * type is defined by the Number implementation in question.
 *
 * For platform classes, the conversion is often analogous to a
 * narrowing primitive conversion or a widening primitive conversion
 * as defining in <cite>The Java&trade; Language Specification</cite>
 * for converting between primitive types.  Therefore, conversions may
 * lose information about the overall magnitude of a numeric value, may
 * lose precision, and may even return a result of a different sign
 * than the input.
 *
 * See the documentation of a given Number implementation for
 * conversion details.
 *
 * @author      Lee Boynton
 * @author      Arthur van Hoff
 */
abstract class TNumber extends TObject implements Serializable {

    /**
     * Returns the value of the specified number as an int,
     * which may involve rounding or truncation.
     *
     * @return  int the numeric value represented by this object after conversion
     *          to type int.
     */
    public abstract function intValue(): int;

    /**
     * Returns the value of the specified number as a float,
     * which may involve rounding.
     *
     * @return  float the numeric value represented by this object after conversion
     *          to type float.
     */
    public abstract function floatValue(): float;
}