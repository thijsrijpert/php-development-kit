<?php

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
     * Returns the value of the specified number as a long,
     * which may involve rounding or truncation.
     *
     * @return  int the numeric value represented by this object after conversion
     *          to type long.
     */
    public abstract function longValue(): int;

    /**
     * Returns the value of the specified number as a float,
     * which may involve rounding.
     *
     * @return  float the numeric value represented by this object after conversion
     *          to type float.
     */
    public abstract function floatValue(): float;

    /**
     * Returns the value of the specified number as a double,
     * which may involve rounding.
     *
     * @return  float the numeric value represented by this object after conversion
     *          to type double.
     */
    public abstract function doubleValue(): float;

    /**
     * Returns the value of the specified number as a byte,
     * which may involve rounding or truncation.
     *
     * @return  int the numeric value represented by this object after conversion
     *          to type byte.
     */
    public function byteValue(): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Returns the value of the specified number as a short,
     * which may involve rounding or truncation.
     *
     *
     * @return  int the numeric value represented by this object after conversion
     *          to type short.
     */
    public function shortValue(): int {
        throw new UnsupportedOperationException();
    }
}