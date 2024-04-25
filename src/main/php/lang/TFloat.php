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

use jhp\lang\exception\NumberFormatException;
use jhp\lang\exception\UnsupportedOperationException;
use jhp\lang\internal\GType;
use TypeError;

/**
* The Float class wraps a value of primitive type
* float in an object. An object of type
* Float contains a single field whose type is
* float.
*
* <p>In addition, this class provides several methods for converting a
* float to a String and a
* String to a float, as well as other
* constants and methods useful when dealing with a
* float.
*
* <p>This is a <a href="{@docRoot}/java.base/java/lang/doc-files/ValueBased.html">value-based</a>
* class; programmers should treat instances that are
* {@linkplain #equals(Object) equal} as interchangeable and should not
* use instances for synchronization, or unpredictable behavior may
* occur. For example, in a future release, synchronization may fail.
*
*
* The class java.lang.Double has a <a
    * href="Double.html#equivalenceRelation">discussion of equality,
    * equivalence, and comparison of floating-point values</a> that is
* equality applicable to float values.
*
*/
class TFloat extends TNumber implements Comparable {

    /**
    * A constant holding the positive infinity of type
    * float. It is equal to the value returned by
    * Float.intBitsToFloat(0x7f800000).
     */
    public const POSITIVE_INFINITY = INF;

    /**
    * A constant holding the negative infinity of type
    * float. It is equal to the value returned by
    * Float.intBitsToFloat(0xff800000).
     */
    public const NEGATIVE_INFINITY = -INF;

    /**
    * A constant holding a Not-a-Number (NaN) value of type
    * float.  It is equivalent to the value returned by
    * Float.intBitsToFloat(0x7fc00000).
     */
    public const NaN = NAN;

    public const MAX_VALUE = PHP_FLOAT_MAX;

    /**
    * A constant holding the smallest positive normal value of type
    * float, 2<sup>-126</sup>.  It is equal to the
    * hexadecimal floating-point literal 0x1.0p-126f and also
    * equal to Float.intBitsToFloat(0x00800000).
    *
    * @since 1.6
    */
    public const MIN_NORMAL = PHP_FLOAT_MIN;

    /**
    * A constant holding the smallest positive nonzero value of type
    * float, 2<sup>-149</sup>. It is equal to the
    * hexadecimal floating-point literal 0x0.000002P-126f
    * and also equal to Float.intBitsToFloat(0x1).
    */
    public const MIN_VALUE = PHP_FLOAT_EPSILON;

    /**
    * Maximum exponent a finite float variable may have.  It
    * is equal to the value returned by {@code
    * Math.getExponent(Float.MAX_VALUE)}.
    *
    * @since 1.6
    */
    public const MAX_EXPONENT = null;

    /**
    * Minimum exponent a normalized float variable may have.
    * It is equal to the value returned by {@code
    * Math.getExponent(Float.MIN_NORMAL)}.
    *
    * @since 1.6
    */
    public const MIN_EXPONENT = null;

    /**
    * The number of bits used to represent a float value.
    *
    * @since 1.5
    */
    public const SIZE = PHP_INT_SIZE * 8;

    /**
    * The number of bytes used to represent a float value.
    *
    * @since 1.8
    */
    public const BYTES = PHP_INT_SIZE;

    /**
    * The Class instance representing the primitive type
    * float.
    *
    * @since 1.1
    */
    public const TYPE = null;

    private function __construct(private readonly float $value) {

    }

    /**
    * Returns a string representation of the float
    * argument. All characters mentioned below are ASCII characters.
    * <ul>
    * <li>If the argument is NaN, the result is the string
    * "NaN".
    * <li>Otherwise, the result is a string that represents the sign and
    *     magnitude (absolute value) of the argument. If the sign is
    *     negative, the first character of the result is
    *     '-' ('\u005Cu002D'); if the sign is
    *     positive, no sign character appears in the result. As for
    *     the magnitude <i>m</i>:
    * <ul>
    * <li>If <i>m</i> is infinity, it is represented by the characters
    *     "Infinity"; thus, positive infinity produces
    *     the result "Infinity" and negative infinity
    *     produces the result "-Infinity".
    * <li>If <i>m</i> is zero, it is represented by the characters
    *     "0.0"; thus, negative zero produces the result
    *     "-0.0" and positive zero produces the result
    *     "0.0".
    * <li> If <i>m</i> is greater than or equal to 10<sup>-3</sup> but
    *      less than 10<sup>7</sup>, then it is represented as the
    *      integer part of <i>m</i>, in decimal form with no leading
    *      zeroes, followed by '.'
    *      ('\u005Cu002E'), followed by one or more
    *      decimal digits representing the fractional part of
    *      <i>m</i>.
    * <li> If <i>m</i> is less than 10<sup>-3</sup> or greater than or
    *      equal to 10<sup>7</sup>, then it is represented in
    *      so-called "computerized scientific notation." Let <i>n</i>
    *      be the unique integer such that 10<sup><i>n</i> </sup>&le;
    *      <i>m</i> {@literal <} 10<sup><i>n</i>+1</sup>; then let <i>a</i>
    *      be the mathematically exact quotient of <i>m</i> and
    *      10<sup><i>n</i></sup> so that 1 &le; <i>a</i> {@literal <} 10.
    *      The magnitude is then represented as the integer part of
    *      <i>a</i>, as a single decimal digit, followed by
    *      '.' ('\u005Cu002E'), followed by
    *      decimal digits representing the fractional part of
    *      <i>a</i>, followed by the letter 'E'
    *      ('\u005Cu0045'), followed by a representation
    *      of <i>n</i> as a decimal integer, as produced by the
    *      method {@link java.lang.Integer#toString(int)}.
    *
    * </ul>
    * </ul>
    * How many digits must be printed for the fractional part of
    * <i>m</i> or <i>a</i>? There must be at least one digit
    * to represent the fractional part, and beyond that as many, but
    * only as many, more digits as are needed to uniquely distinguish
    * the argument value from adjacent values of type
    * float. That is, suppose that <i>x</i> is the
    * exact mathematical value represented by the decimal
    * representation produced by this method for a finite nonzero
    * argument <i>f</i>. Then <i>f</i> must be the float
    * value nearest to <i>x</i>; or, if two float values are
    * equally close to <i>x</i>, then <i>f</i> must be one of
    * them and the least significant bit of the significand of
    * <i>f</i> must be 0.
    *
    * <p>To create localized string representations of a floating-point
    * value, use subclasses of {@link java.text.NumberFormat}.
    *
    * @param  float $f   the float to be converted.
    * @return string a string representation of the argument.
    */
    public static function asString(float $f): string {
        if ($f === TFloat::NaN) {
            return "NaN";
        } else if ($f === TFloat::POSITIVE_INFINITY) {
            return "Infinity";
        } else if ($f === TFloat::NEGATIVE_INFINITY) {
            return "-Infinity";
        }
        return (string) $f;
    }

    /**
    * Returns a hexadecimal string representation of the
    * float argument. All characters mentioned below are
    * ASCII characters.
    **/
    public static function toHexString(float $f): string {
        return "0x000";
    }

    /**
    * Returns a Float object holding the
    * float value represented by the argument string
    * s.
    *
    * <p>If s is null, then a
        * NullPointerException is thrown.
        *
        * <p>Leading and trailing whitespace characters in s
        * are ignored.  Whitespace is removed as if by the {@link
        * String#trim} method; that is, both ASCII space and control
        * characters are removed. The rest of s should
        * constitute a <i>FloatValue</i> as described by the lexical
        * syntax rules:
        *
        * <blockquote>
        * <dl>
            * <dt><i>FloatValue:</i>
                * <dd><i>Sign<sub>opt</sub></i> NaN
                * <dd><i>Sign<sub>opt</sub></i> Infinity
                * <dd><i>Sign<sub>opt</sub> FloatingPointLiteral</i>
                * <dd><i>Sign<sub>opt</sub> HexFloatingPointLiteral</i>
                * <dd><i>SignedInteger</i>
                * </dl>
        *
        * <dl>
            * <dt><i>HexFloatingPointLiteral</i>:
                * <dd> <i>HexSignificand BinaryExponent FloatTypeSuffix<sub>opt</sub></i>
                * </dl>
        *
        * <dl>
            * <dt><i>HexSignificand:</i>
                * <dd><i>HexNumeral</i>
                * <dd><i>HexNumeral</i> .
                * <dd>0x <i>HexDigits<sub>opt</sub>
                    *     </i>.<i> HexDigits</i>
                * <dd>0X<i> HexDigits<sub>opt</sub>
                    *     </i>. <i>HexDigits</i>
                * </dl>
        *
        * <dl>
            * <dt><i>BinaryExponent:</i>
                * <dd><i>BinaryExponentIndicator SignedInteger</i>
                * </dl>
        *
        * <dl>
            * <dt><i>BinaryExponentIndicator:</i>
                * <dd>p
                * <dd>P
                * </dl>
        *
        * </blockquote>
    *
    * where <i>Sign</i>, <i>FloatingPointLiteral</i>,
    * <i>HexNumeral</i>, <i>HexDigits</i>, <i>SignedInteger</i> and
    * <i>FloatTypeSuffix</i> are as defined in the lexical structure
    * sections of
    * <cite>The Java Language Specification</cite>,
    * except that underscores are not accepted between digits.
    * If s does not have the form of
    * a <i>FloatValue</i>, then a NumberFormatException
    * is thrown. Otherwise, s is regarded as
    * representing an exact decimal value in the usual
    * "computerized scientific notation" or as an exact
    * hexadecimal value; this exact numerical value is then
    * conceptually converted to an "infinitely precise"
    * binary value that is then rounded to type float
    * by the usual round-to-nearest rule of IEEE 754 floating-point
    * arithmetic, which includes preserving the sign of a zero
    * value.
    *
    * Note that the round-to-nearest rule also implies overflow and
    * underflow behaviour; if the exact value of s is large
    * enough in magnitude (greater than or equal to ({@link
    * #MAX_VALUE} + {@link Math#ulp(float) ulp(MAX_VALUE)}/2),
    * rounding to float will result in an infinity and if the
    * exact value of s is small enough in magnitude (less
    * than or equal to {@link #MIN_VALUE}/2), rounding to float will
    * result in a zero.
    *
    * Finally, after rounding a Float object representing
    * this float value is returned.
    *
    * <p>To interpret localized string representations of a
    * floating-point value, use subclasses of {@link
    * java.text.NumberFormat}.
    *
    * <p>Note that trailing format specifiers, specifiers that
    * determine the type of a floating-point literal
    * (1.0f is a float value;
    * 1.0d is a double value), do
    * <em>not</em> influence the results of this method.  In other
    * words, the numerical value of the input string is converted
    * directly to the target floating-point type.  In general, the
    * two-step sequence of conversions, string to double
    * followed by double to float, is
    * <em>not</em> equivalent to converting a string directly to
    * float.  For example, if first converted to an
    * intermediate double and then to
    * float, the string<br>
    * "1.00000017881393421514957253748434595763683319091796875001d"<br>
    * results in the float value
    * 1.0000002f; if the string is converted directly to
    * float, <code>1.000000<b>1</b>f</code> results.
    *
    * <p>To avoid calling this method on an invalid string and having
    * a NumberFormatException be thrown, the documentation
    * for {@link Double#valueOf Double.valueOf} lists a regular
    * expression which can be used to screen the input.
    *
    * @param   string|float $s   the string to be parsed.
    * @return  TFloat a Float object holding the value
    *          represented by the String argument.
    * @throws  NumberFormatException  if the string does not contain a
    *          parsable number.
    */
    public static function valueOf(string|float $s): TFloat {
        if (GType::of($s)->isString()) {
            $s = TFloat::parseFloat($s);
        }
        return new TFloat($s);
    }

    /**
    * Returns a new float initialized to the value
    * represented by the specified String, as performed
    * by the valueOf method of class Float.
    *
    * @param  string $s the string to be parsed.
    * @return float the float value represented by the string
    *         argument.
    * @throws NumberFormatException if the string does not contain a
    *               parsable float.
    * @see    java.lang.Float#valueOf(String)
    */
    public static function parseFloat(string $s): float {
        return  0.0;
    }

    /**
    * Returns true if the specified number is a
    * Not-a-Number (NaN) value, false otherwise.
    *
    * @param   float $v   the value to be tested.
    * @return  bool true if the argument is NaN;
    *          false otherwise.
    */
    public static function checkIsNaN(float $v): bool {
        return is_nan($v);
    }

    /**
    * Returns true if the specified number is infinitely
    * large in magnitude, false otherwise.
    *
    * @param   float $v   the value to be tested.
    * @return  true if the argument is positive infinity or
    *          negative infinity; false otherwise.
    */
    public static function checkIsInfinite(float $v): bool {
        return is_infinite($v);
    }


    /**
    * Returns true if the argument is a finite floating-point
    * value; returns false otherwise (for NaN and infinity
    * arguments).
    *
    * @param float $f the float value to be tested
    * @return bool true if the argument is a finite
    * floating-point value, false otherwise.
    * @since 1.8
    */
    public static function checkIsFinite(float $f): bool {
        return is_finite($f);
    }

    /**
    * Returns true if this Float value is a
    * Not-a-Number (NaN), false otherwise.
    *
    * @return  true if the value represented by this object is
    *          NaN; false otherwise.
    */
    public function isNaN(): bool {
        return TFloat::checkIsNaN($this->value);
    }

    /**
    * Returns true if this Float value is
    * infinitely large in magnitude, false otherwise.
    *
    * @return  true if the value represented by this object is
    *          positive infinity or negative infinity;
    *          false otherwise.
    */
    public function isInfinite(): bool {
        return TFloat::checkIsInfinite($this->value);
    }

    /**
    * Returns a string representation of this Float object.
    * The primitive float value represented by this object
    * is converted to a String exactly as if by the method
    * toString of one argument.
    *
    * @return string a String representation of this object.
    * @see java.lang.Float#toString(float)
    */
    public function toString(): string {
        return TFloat::asString($this->value);
    }

    /**
    * Returns the value of this Float as a byte after
    * a narrowing primitive conversion.
    *
    * @return  int the float value represented by this object
    *          converted to type byte
    */
    public function byteValue(): int {
        throw new UnsupportedOperationException();
    }

    /**
    * Returns the value of this Float as a short
    * after a narrowing primitive conversion.
    *
    * @return  int the float value represented by this object
    *          converted to type short
    * @jls 5.1.3 Narrowing Primitive Conversion
    * @since 1.1
    */
    public function shortValue(): int {
        throw new UnsupportedOperationException();
    }

    /**
    * Returns the value of this Float as an int after
    * a narrowing primitive conversion.
    *
    * @return  int the float value represented by this object
    *          converted to type int
    * @jls 5.1.3 Narrowing Primitive Conversion
    */
    public function intValue(): int {
        return (int) $this->value;
    }

    /**
    * Returns value of this Float as a long after a
    * narrowing primitive conversion.
    *
    * @return  int the float value represented by this object
    *          converted to type long
    * @jls 5.1.3 Narrowing Primitive Conversion
    */
    public function longValue(): int {
        throw new UnsupportedOperationException();
    }

    /**
    * Returns the float value of this Float object.
    *
    * @return float the float value represented by this object
    */
    public function floatValue(): float {
        return $this->value;
    }

    /**
    * Returns the value of this Float as a double
    * after a widening primitive conversion.
    *
    * @return float the float value represented by this
    *         object converted to type double
    */
    public function doubleValue(): float {
        throw new UnsupportedOperationException();
    }

    /**
    * Returns a hash code for this Float object. The
    * result is the integer bit representation, exactly as produced
    * by the method {@link #floatToIntBits(float)}, of the primitive
    * float value represented by this Float
    * object.
    *
    * @return int a hash code value for this object.
    */
    public function hashCode(): int {
        throw new UnsupportedOperationException();
    }

    /**
    * Returns a hash code for a float value; compatible with
    * Float.hashCode().
    *
    * @param float $value the value to hash
    * @return int a hash code value for a float value.
    * @since 1.8
    */
    public static function asHashCode(float $value): int {
        throw new UnsupportedOperationException();
    }

    /**
    * Compares this object against the specified object.  The result
    * is true if and only if the argument is not
    * null and is a Float object that
    * represents a float with the same value as the
    * float represented by this object. For this
    * purpose, two float values are considered to be the
    * same if and only if the method {@link #floatToIntBits(float)}
    * returns the identical int value when applied to
    * each.
    *
    * @apiNote
    * This method is defined in terms of {@link
    * #floatToIntBits(float)} rather than the == operator on
    * float values since the == operator does
    * <em>not</em> define an equivalence relation and to satisfy the
    * {@linkplain Object#equals equals contract} an equivalence
    * relation must be implemented; see <a
        * href="Double.html#equivalenceRelation">this discussion</a> for
    * details of floating-point equality and equivalence.
    *
    * @param ?IObject $obj the object to be compared
    * @return bool true if the objects are the same;
    *          false otherwise.
    */
    public function equals(?IObject $obj = null): bool {
        if ($obj === null) {
            return false;
        }
        return $obj instanceof TFloat && $obj->value === $this->value;
    }

    public static function floatToIntBits(float $value): int {
        if (!TFloat::checkIsNaN($value)) {
            return TFloat::floatToRawIntBits($value);
        }
        return 0x7fc00000;
    }

    public static function floatToRawIntBits(float $value): int {
        return 0;
    }

    public static function intBitsToFloat(int $bits): float {
        return 0.0;
    }

    /**
    * Compares two Float objects numerically.
    *
    * This method imposes a total order on Float objects
    * with two differences compared to the incomplete order defined by
    * the Java language numerical comparison operators ({@code <, <=,
    * ==, >=, >}) on float values.
    *
    * <ul><li> A NaN is <em>unordered</em> with respect to other
        *          values and unequal to itself under the comparison
        *          operators.  This method chooses to define {@code
        *          Float.NaN} to be equal to itself and greater than all
        *          other double values (including {@code
        *          Float.POSITIVE_INFINITY}).
        *
        *      <li> Positive zero and negative zero compare equal
        *      numerically, but are distinct and distinguishable values.
        *      This method chooses to define positive zero (+0.0f),
        *      to be greater than negative zero (-0.0f).
        * </ul>
    *
    * This ensures that the <i>natural ordering</i> of Float
    * objects imposed by this method is <i>consistent with
    * equals</i>; see <a href="Double.html#equivalenceRelation">this
    * discussion</a> for details of floating-point comparison and
    * ordering.
    *
    *
    * @param   IObject $o the Float to be compared.
    * @return  int the value 0 if anotherFloat is
    *          numerically equal to this Float; a value
    *          less than 0 if this Float
    *          is numerically less than anotherFloat;
    *          and a value greater than 0 if this
    *          Float is numerically greater than
    *          anotherFloat.
    *
    * @jls 15.20.1 Numerical Comparison Operators <, <=, >, and >=
    * @since   1.2
    */
    public function compareTo(IObject $o): int {
        if ($o instanceof TFloat) {
            return $this->value <=> $o->value;
        }

        throw new TypeError("Trying to compare TFloat to: " . TClass::of($o)->getName());
    }

    /**
    * Compares the two specified float values. The sign
    * of the integer value returned is the same as that of the
    * integer that would be returned by the call:
    * <pre>
    *    new Float(f1).compareTo(new Float(f2))
    * </pre>
    *
    * @param   float $f1        the first float to compare.
    * @param   float $f2        the second float to compare.
    * @return  int the value 0 if f1 is
    *          numerically equal to f2; a value less than
    *          0 if f1 is numerically less than
    *          f2; and a value greater than 0
    *          if f1 is numerically greater than
    *          f2.
    */
    public static function compare(float $f1, float $f2): int {
        return $f1 <=> $f2;
    }

    /**
    * Adds two float values together as per the + operator.
    *
    * @param float $a the first operand
    * @param float $b the second operand
    * @return float the sum of a and b
    */
    public static function sum(float $a, float $b): float {
        return $a + $b;
    }

    /**
    * Returns the greater of two float values
    * as if by calling {@link Math#max(float, float) Math.max}.
    *
    * @param float $a the first operand
    * @param float $b the second operand
    * @return float the greatest of a and b
    */
    public static function max(float $a, float $b): float {
        return max($a, $b);
    }

    /**
    * Returns the smaller of two float values
    * as if by calling {@link Math#min(float, float) Math.min}.
    *
    * @param float $a the first operand
    * @param float $b the second operand
    * @return float the smallest of a and b
    */
    public static function min(float $a, float $b): float {
        return min($a, $b);
    }
}
