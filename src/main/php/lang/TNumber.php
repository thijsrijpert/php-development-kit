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
 * Copyright (c) 1994, 2021, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */

namespace jhp\lang;

use jhp\io\Serializable;
use jhp\lang\exception\UnsupportedOperationException;

/**
 * The abstract class Number is the superclass of platform
 * classes representing numeric values that are convertible to the
 * primitive types byte, double, float, int, long, and short.
 *
 * See the documentation of a given Number implementation for
 * conversion details.
 */
abstract class TNumber extends TObject implements Serializable {

    /**
     * Returns the value of the specified number as an int,
     * which may involve rounding or truncation.
     *
     * @return  int the numeric value represented by this object after conversion
     *          to type int.
     */
    abstract public function intValue(): int;

    /**
     * Returns the value of the specified number as a float,
     * which may involve rounding.
     *
     * @return  float the numeric value represented by this object after conversion
     *          to type float.
     */
    abstract public function floatValue(): float;
}