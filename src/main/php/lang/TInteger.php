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

use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\exception\NumberFormatException;
use jhp\lang\exception\UnsupportedOperationException;
use jhp\lang\internal\GType;
use RuntimeException;

/**
 * The Integer class wraps a value of the primitive type
 * int in an object. An object of type Integer
 * contains a single field whose type is int.
 *
 * <p>In addition, this class provides several methods for converting
 * an int to a String and a String to an
 * int, as well as other constants and methods useful when
 * dealing with an int.
 *
 * In PHP an integer can be either 32 or 64 bit depending on compiled version and CPU architecture.
 * So TInteger can be seen as both an implementation of Integer and Long in Java.
 *
 * <p>Implementation note: The implementations of the "bit twiddling"
 * methods (such as {@link TInteger::highestOneBit()} and
 * {@link TInteger::numberOfTrailingZeros()}) are
 * based on material from Henry S. Warren, Jr.'s <i>Hacker's
 * Delight</i>, (Addison Wesley, 2002).
 */
class TInteger extends TNumber implements Comparable {

    /**
     * A constant holding the minimum value an int can have
     */
    public const MIN_VALUE = PHP_INT_MIN;

    /**
     * A constant holding the maximum value an int can
     * have, 2<sup>31</sup>-1.
     */
    public const MAX_VALUE = PHP_INT_MAX;

    /**
     * All possible chars for representing a number as a String
     */
    private const reverseDigits = [
        '0' => 0 , '1' => 1 , '2' => 2 , '3' => 3, '4' => 4 , '5' => 5,
        '6' => 6, '7' => 7, '8' => 8, '9' => 9, 'a' => 10, 'b' => 11,
        'c' => 12, 'd' => 13, 'e' => 14, 'f' => 15, 'g' => 16, 'h' => 17,
        'i' => 18, 'j' => 19, 'k' => 20, 'l' => 21, 'm' => 22, 'n' => 23,
        'o' => 24, 'p' => 25, 'q' => 26, 'r' => 27, 's' => 28, 't' => 29,
        'u' => 30, 'v' => 31, 'w' => 32, 'x' => 33, 'y' => 34, 'z' => 35
    ];

    /**
     * All possible chars for representing a number as a String
     */
    private const digits = [
        '0', '1', '2', '3', '4', '5',
        '6', '7', '8', '9', 'a', 'b',
        'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l', 'm', 'n',
        'o', 'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y', 'z'
    ];

    /**
     * Constructor for the integer wrapper class, use valueOf to create a new instance
     * @param int $value the int value to store
     */
    private function  __construct(private readonly int $value) { }

    /**
     * Returns a string representation of the first argument in the
     * radix specified by the second argument.
     *
     * <p>If the radix is smaller than Character.MIN_RADIX
     * or larger than Character.MAX_RADIX, then the radix
     * 10 is used instead.
     *
     * <p>If the first argument is negative, the first element of the
     * result is the ASCII minus character '-'
     * ('\u005Cu002D'). If the first argument is not
     * negative, no sign character appears in the result.
     *
     * <p>The remaining characters of the result represent the magnitude
     * of the first argument. If the magnitude is zero, it is
     * represented by a single zero character '0'
     * ('\u005Cu0030'); otherwise, the first character of
     * the representation of the magnitude will not be the zero
     * character.  The following ASCII characters are used as digits:
     *
     * <blockquote>
     *   0123456789abcdefghijklmnopqrstuvwxyz
     * </blockquote>
     *
     * These are '\u005Cu0030' through
     * '\u005Cu0039' and '\u005Cu0061' through
     * '\u005Cu007A'. If radix is
     * <var>N</var>, then the first <var>N</var> of these characters
     * are used as radix-<var>N</var> digits in the order shown. Thus,
     * the digits for hexadecimal (radix 16) are
     * 0123456789abcdef. If uppercase letters are
     * desired, the {@link java.lang.String#toUpperCase()} method may
     * be called on the result:
     *
     * <blockquote>
     *  Integer.toString(n, 16).toUpperCase()
     * </blockquote>
     *
     * @param   int     $i      an integer to be converted to a string.
     * @param   int     $radix  the radix to use in the string representation.
     *
     * @return  string representation of the argument in the specified radix.
     * @see     TCharacter::MAX_RADIX
     * @see     Tharacter::MIN_RADIX
     */
    public static function asString(int $i, int $radix = 10): string {
        if ($radix == 10 ||
            $radix < TCharacter::MIN_RADIX ||
            $radix > TCharacter::MAX_RADIX
        ) {
            return (string) $i;
        }

        if ($i === 0) {
            return 0;
        } elseif ($i > 0) {
            return base_convert((string)$i, 10, $radix);
        }

        $i = $i * -1;
        return "-" . base_convert((string) $i, 10, $radix);
    }

    /**
     * Returns a string representation of the first argument as an
     * unsigned integer value in the radix specified by the second
     * argument.
     *
     * <p>If the radix is smaller than Character.MIN_RADIX
     * or larger than Character.MAX_RADIX, then the radix
     * 10 is used instead.
     *
     * <p>Note that since the first argument is treated as an unsigned
     * value, no leading sign character is printed.
     *
     * <p>If the magnitude is zero, it is represented by a single zero
     * character '0' ('\u005Cu0030'); otherwise,
     * the first character of the representation of the magnitude will
     * not be the zero character.
     *
     * <p>The behavior of radixes and the characters used as digits
     * are the same as {@link #toString(int, int) toString}.
     *
     * @param   int $i       an integer to be converted to an unsigned string.
     * @param   int $radix   the radix to use in the string representation.
     * @return  string an unsigned string representation of the argument in the specified radix.
     */
    public static function toUnsignedString(int $i, int $radix): string {
        if ($i >= 0) {
            return TInteger::asString($i, $radix);
        }

        return match ($radix) {
            2 => TInteger::toBinaryString($i),
            4 => TInteger::toUnsignedString0($i, 2),
            8 => TInteger::toOctalString($i),
            16 => TInteger::toHexString($i),
            32 => TInteger::toUnsignedString0($i, 5),
            default => throw new UnsupportedOperationException("Radix $radix is not yet supported")
        };
    }

    /**
     * Returns a string representation of the integer argument as an
     * unsigned integer in base 16.
     *
     * <p>The unsigned integer value is the argument plus 2<sup>32</sup>
     * if the argument is negative; otherwise, it is equal to the
     * argument.  This value is converted to a string of ASCII digits
     * in hexadecimal (base 16) with no extra leading
     * 0s.
     *
     * <p>The value of the argument can be recovered from the returned
     * string s by calling {@link
     * Integer#parseUnsignedInt(String, int)
     * Integer.parseUnsignedInt(s, 16)}.
     *
     * <p>If the unsigned magnitude is zero, it is represented by a
     * single zero character '0' ('\u005Cu0030');
     * otherwise, the first character of the representation of the
     * unsigned magnitude will not be the zero character. The
     * following characters are used as hexadecimal digits:
     *
     * <blockquote>
     *  0123456789abcdef
     * </blockquote>
     *
     * These are the characters '\u005Cu0030' through
     * '\u005Cu0039' and '\u005Cu0061' through
     * '\u005Cu0066'. If uppercase letters are
     * desired, the {@link java.lang.String#toUpperCase()} method may
     * be called on the result:
     *
     * <blockquote>
     *  Integer.toHexString(n).toUpperCase()
     * </blockquote>
     *
     * @param   int $i   an integer to be converted to a string.
     * @return  string the string representation of the unsigned integer value
     *          represented by the argument in hexadecimal (base&nbsp;16).
     * @see TInteger::parseUnsignedInt(String, int)
     * @see TInteger::toUnsignedString(int, int)
     */
    public static function toHexString(int $i): string {
        return TInteger::toUnsignedString0($i, 4);
    }

    /**
     * Returns a string representation of the integer argument as an
     * unsigned integer in base 8.
     *
     * <p>The unsigned integer value is the argument plus 2<sup>32</sup>
     * if the argument is negative; otherwise, it is equal to the
     * argument.  This value is converted to a string of ASCII digits
     * in octal (base 8) with no extra leading 0s.
     *
     * <p>The value of the argument can be recovered from the returned
     * string s by calling {@link
     * Integer#parseUnsignedInt(String, int)
     * Integer.parseUnsignedInt(s, 8)}.
     *
     * <p>If the unsigned magnitude is zero, it is represented by a
     * single zero character '0' ('\u005Cu0030');
     * otherwise, the first character of the representation of the
     * unsigned magnitude will not be the zero character. The
     * following characters are used as octal digits:
     *
     * <blockquote>
     * 01234567
     * </blockquote>
     *
     * These are the characters '\u005Cu0030' through
     * '\u005Cu0037'.
     *
     * @param   int $i   an integer to be converted to a string.
     * @return  string the string representation of the unsigned integer value
     *          represented by the argument in octal (base&nbsp;8).
     * @see TInteger::parseUnsignedInt(String, int)
     * @see TInteger::toUnsignedString(int, int)
     */
    public static function toOctalString(int $i): string {
        return TInteger::toUnsignedString0($i, 3);
    }

    /**
     * Returns a string representation of the integer argument as an
     * unsigned integer in base 2.
     *
     * <p>The unsigned integer value is the argument plus 2<sup>32</sup>
     * if the argument is negative; otherwise it is equal to the
     * argument.  This value is converted to a string of ASCII digits
     * in binary (base 2) with no extra leading 0s.
     *
     * <p>The value of the argument can be recovered from the returned
     * string s by calling {@link
     * Integer#parseUnsignedInt(String, int)
     * Integer.parseUnsignedInt(s, 2)}.
     *
     * <p>If the unsigned magnitude is zero, it is represented by a
     * single zero character '0' ('\u005Cu0030');
     * otherwise, the first character of the representation of the
     * unsigned magnitude will not be the zero character. The
     * characters '0' ('\u005Cu0030') and {@code
     * '1'} ('\u005Cu0031') are used as binary digits.
     *
     * @param   int $i   an integer to be converted to a string.
     * @return  string the string representation of the unsigned integer value
     *          represented by the argument in binary (base&nbsp;2).
     * @see TInteger::parseUnsignedInt(String, int)
     * @see TInteger::toUnsignedString(int, int)
     */
    public static function toBinaryString(int $i): string
    {
        return TInteger::toUnsignedString0($i, 1);
    }

    /**
     * Convert the integer to an unsigned number.
     */
    private static function toUnsignedString0(int $value, int $shift): string {
        if ($shift < 1 || $shift > 5) {
            throw new RuntimeException("Illegal Shift value");
        }
        if ($value === 0) {
            return "0";
        }

        // Calculate the length
        $length = TInteger::SIZE - TInteger::numberOfLeadingZeros($value);
        $length += $shift - 1;
        $length /= $shift;
        $length = TInteger::max($length, 1);

        // Calculate the mask
        $radix = 1 << $shift;
        $mask = $radix - 1;

        // Calculate the result
        $result = "";
        while ($value != 0 && $length > 0) {
            $result[--$length] = TInteger::digits[$value & $mask];
            $value = TInteger::unsignedRightShift($value, $shift);
        }

        return $result;
    }

    /**
     * Parses the string argument as a signed integer in the radix
     * specified by the second argument. The characters in the string
     * must all be digits of the specified radix (as determined by
     * whether {@link java.lang.Character#digit(char, int)} returns a
     * nonnegative value), except that the first character may be an
     * ASCII minus sign '-' ('\u005Cu002D') to
     * indicate a negative value or an ASCII plus sign '+'
     * ('\u005Cu002B') to indicate a positive value. The
     * resulting integer value is returned.
     *
     * <p>An exception of type NumberFormatException is
     * thrown if any of the following situations occurs:
     * <ul>
     * <li>The first argument is null or is a string of
     * length zero.
     *
     * <li>The radix is either smaller than
     * {@link java.lang.Character#MIN_RADIX} or
     * larger than {@link java.lang.Character#MAX_RADIX}.
     *
     * <li>Any character of the string is not a digit of the specified
     * radix, except that the first character may be a minus sign
     * '-' ('\u005Cu002D') or plus sign
     * '+' ('\u005Cu002B') provided that the
     * string is longer than length 1.
     *
     * <li>The value represented by the string is not a value of type
     * int.
     * </ul>
     *
     * Using values larger than Integer.MAX_VALUE results in unstable values, these should not be supplied
     *
     * <p>Examples:
     * <blockquote><pre>
     * parseInt("0", 10) returns 0
     * parseInt("473", 10) returns 473
     * parseInt("+42", 10) returns 42
     * parseInt("-0", 10) returns 0
     * parseInt("-FF", 16) returns -255
     * parseInt("1100110", 2) returns 102
     * parseInt("2147483647", 10) returns 2147483647
     * parseInt("-2147483648", 10) returns -2147483648
     * parseInt("99", 8) throws a NumberFormatException
     * parseInt("Kona", 10) throws a NumberFormatException
     * parseInt("Kona", 27) returns 411787
     * </pre></blockquote>
     *
     * @param      string $s   the String containing the integer
     *                  representation to be parsed
     * @param      int $radix   the radix to be used while parsing s.
     * @return     int the integer represented by the string argument in the
     *             specified radix.
     * @throws  NumberFormatException if the String
     *              does not contain a parsable int.
     */
    public static function parseInt(string $s, int $radix = 10): int {
        if ($radix < TCharacter::MIN_RADIX) {
            throw new NumberFormatException("radix " . $radix . " less than Character.MIN_RADIX");
        }

        if ($radix > TCharacter::MAX_RADIX) {
            throw new NumberFormatException("radix " . $radix . " greater than Character.MAX_RADIX");
        }

        if (strlen($s) <= 0) {
            throw NumberFormatException::forInputString($s);
        }

        $negative = false;
        if ($s[0] === "-") {
            $negative = true;
        }

        if($s[0] === "-" || $s === "+") {
            $s = substr($s, 1);
        }

        for ($i = 0; $i < strlen($s); $i++) {
            if (self::reverseDigits[strtolower($s[$i])] >= $radix) {
                throw NumberFormatException::forInputString($s);
            }
        }

        $value = (int) base_convert($s, $radix, 10);
        return $negative ? $value * -1 : $value;
    }

    /**
     * Parses the string argument as an unsigned integer in the radix
     * specified by the second argument.  An unsigned integer maps the
     * values usually associated with negative numbers to positive
     * numbers larger than MAX_VALUE.
     *
     * The characters in the string must all be digits of the
     * specified radix (as determined by whether {@link
     * java.lang.Character#digit(char, int)} returns a non-negative
     * value), except that the first character may be an ASCII plus
     * sign '+' ('\u005Cu002B'). The resulting
     * integer value is returned.
     *
     * <p>An exception of type NumberFormatException is
     * thrown if any of the following situations occurs:
     * <ul>
     * <li>The first argument is null or is a string of
     * length zero.
     *
     * <li>The radix is either smaller than
     * {@link java.lang.Character#MIN_RADIX} or
     * larger than {@link java.lang.Character#MAX_RADIX}.
     *
     * <li>Any character of the string is not a digit of the specified
     * radix, except that the first character may be a plus sign
     * '+' ('\u005Cu002B') provided that the
     * string is longer than length 1.
     *
     * <li>The value represented by the string is larger than the
     * largest unsigned int, 2<sup>32</sup>-1.
     *
     * </ul>
     *
     *
     * @param      string $s   the String containing the unsigned integer
     *                  representation to be parsed
     * @param      int $radix   the radix to be used while parsing s.
     * @return     int the integer represented by the string argument in the
     *             specified radix.
     * @throws     NumberFormatException if the String
     *             does not contain a parsable int.
     * @since 1.8
     */
    public static function parseUnsignedInt(string $s, int $radix): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Returns an Integer object holding the value
     * extracted from the specified String when parsed
     * with the radix given by the second argument. The first argument
     * is interpreted as representing a signed integer in the radix
     * specified by the second argument, exactly as if the arguments
     * were given to the {@link #parseInt(java.lang.String, int)}
     * method. The result is an Integer object that
     * represents the integer value specified by the string.
     *
     * <p>In other words, this method returns an Integer
     * object equal to the value of:
     *
     * <blockquote>
     *  new Integer(Integer.parseInt(s, radix))
     * </blockquote>
     *
     * @param string|int $value
     * @param int        $radix the radix to be used in interpreting s
     *
     * @return TInteger Integer object holding the value
     *             represented by the string argument in the specified
     *             radix.
     * @exception NumberFormatException if the String
     *            does not contain a parsable int.
     */
    public static function valueOf(string|int $value, int $radix = 10): TInteger {
        if (GType::of($value)->isInteger() && $radix !== 10) {
            throw new NumberFormatException("Cannot have another radix then 10 on an already parsed int");
        }

        if (GType::of($value)->isString()) {
            $value = TInteger::parseInt($value, $radix);
        }

        return new TInteger($value);
    }

    /**
     * Returns the value of this Integer as an
     * int.
     */
    public function intValue(): int {
        return $this->value;
    }

    /**
     * Returns the value of this Integer as a float
     * after a widening primitive conversion.
     * @jls 5.1.2 Widening Primitive Conversions
     */
    public function floatValue(): float {
        return (float) $this->value;
    }

    /**
     * Returns a String object representing this
     * Integer's value. The value is converted to signed
     * decimal representation and returned as a string, exactly as if
     * the integer value were given as an argument to the
     * Integer::toString() method.
     *
     * @return  string a string representation of the value of this object in
     *          base 10.
     */
    public function toString(): string {
        return TInteger::asString($this->value);
    }

    /**
     * Returns a hash code for this Integer.
     *
     * @return int hash code value for this object, equal to the
     *          primitive int value represented by this
     *          Integer object.
     */
    public function hashCode(): int {
        return $this->value;
    }

    /**
     * Returns a hash code for an int value; compatible with
     * Integer.hashCode().
     *
     * @param int $value
     *
     * @return int hash code value for a int value.
     *
     */
    public static function asHashCode(int $value): int {
        return $value;
    }

    /**
     * Compares this object to the specified object.  The result is
     * true if and only if the argument is not
     * null and is an Integer object that
     * contains the same int value as this object.
     *
     * @param IObject|null $obj the object to compare with.
     *
     * @return  true if the objects are the same;
     *          false otherwise.
     */
    public function equals(?IObject $obj = null): bool {
        if ($obj === null) {
            return false;
        }
        if ($obj instanceof TInteger) {
            return $this->value === $obj->intValue();
        }
        return false;
    }

    /**
     * Decodes a String into an Integer.
     * Accepts decimal, hexadecimal, and octal numbers given
     * by the following grammar:
     *
     * <blockquote>
     * <dl>
     * <dt><i>Number:</i>
     * <dd><i>Sign<sub>opt</sub> DecimalNumeral</i>
     * <dd><i>Sign<sub>opt</sub></i> 0x <i>HexDigits</i>
     * <dd><i>Sign<sub>opt</sub></i> 0X <i>HexDigits</i>
     * <dd><i>Sign<sub>opt</sub></i> # <i>HexDigits</i>
     * <dd><i>Sign<sub>opt</sub></i> 0 <i>OctalDigits</i>
     *
     * <dt><i>Sign:</i>
     * <dd>-
     * <dd>+
     * </dl>
     * </blockquote>
     *
     * <i>DecimalNumeral</i>, <i>HexDigits</i>, and <i>OctalDigits</i>
     * are as defined in section 3.10.1 of
     * <cite>The Java&trade; Language Specification</cite>,
     * except that underscores are not accepted between digits.
     *
     * <p>The sequence of characters following an optional
     * sign and/or radix specifier ("0x", "0X",
     * "#", or leading zero) is parsed as by the {@code
     * Integer.parseInt} method with the indicated radix (10, 16, or
     * 8).  This sequence of characters must represent a positive
     * value or a {@link NumberFormatException} will be thrown.  The
     * result is negated if first character of the specified {@code
     * String} is the minus sign.  No whitespace characters are
     * permitted in the String.
     *
     * @param string $nm
     *
     * @return TInteger Integer object holding the int
     *             value represented by nm
     * @exception NumberFormatException  if the String does not
     *            contain a parsable integer.
     * @see java.lang.Integer#parseInt(java.lang.String, int)
     */
    public static function decode(string $nm): TInteger {
        $radix = 10;
        $index = 0;
        $negative = false;

        if (strlen($nm) == 0){
            throw new NumberFormatException("Zero length string");
        }

        // Handle sign, if present
        if ($nm[0] == '-') {
            $negative = true;
            $index++;
        } elseif ($nm[0] == '+') {
            $index++;
        }

        // Handle radix specifier, if present
        if (substr($nm, $index, 2) === "0x" || substr($nm, $index, 2) === "0X" ) {
            $index += 2;
            $radix = 16;
        } elseif (substr($nm, $index, 1) === "#") {
            $index++;
            $radix = 16;
        } elseif (substr($nm, $index, 1) === "0" && strlen($nm) > 1 + $index) {
            $index++;
            $radix = 8;
        }

        if (substr($nm, $index, 1) === "-" || substr($nm, $index, 1) === "+") {
            throw NumberFormatException::forInputString("Sign character in wrong position:" . $nm);
        }

        $constant = $negative ? "-" . substr($nm, $index) : substr($nm, $index);
        return TInteger::valueOf($constant, $radix);
    }

    /**
     * Compares two Integer objects numerically.
     *
     * @param   IObject $o   the Integer to be compared.
     * @return  int the value 0 if this Integer is
     *          equal to the argument Integer; a value less than
     *          0 if this Integer is numerically less
     *          than the argument Integer; and a value greater
     *          than 0 if this Integer is numerically
     *           greater than the argument Integer (signed
     *           comparison).
     */
    public function compareTo(IObject $o): int {
        if ($o instanceof TInteger) {
            return $this->value <=> $o->value;
        }

        throw new IllegalArgumentException("Trying to compare Integer with: " . TClass::of($o)->getName());
    }

    /**
     * Compares two int values numerically.
     * The value returned is identical to what would be returned by:
     * <pre>
     *    Integer.valueOf(x).compareTo(Integer.valueOf(y))
     * </pre>
     *
     * @param int $x the first int to compare
     * @param int $y the second int to compare
     * @return int the value 0 if x == y;
     *         a value less than 0 if x < y; and
     *         a value greater than 0 if x > y
     * @since 1.7
     */
    public static function compare(int $x, int $y): int {
        return $x <=> $y;
    }

    /**
     * Compares two int values numerically treating the values
     * as unsigned.
     *
     * @param int $x the first int to compare
     * @param int $y the second int to compare
     * @return int the value 0 if x == y; a value less
     *         then 0 if x < y as unsigned values; and
     *         a value greater than 0 if x > y as
     *         unsigned values
     */
    public static function compareUnsigned(int $x, int $y): int {
        return TInteger::compare(TInteger::sum($x, TInteger::MIN_VALUE), TInteger::sum($y, TInteger::MIN_VALUE));
    }

    /**
     * Returns the unsigned quotient of dividing the first argument by
     * the second where each argument and the result is interpreted as
     * an unsigned value.
     *
     * <p>Note that in two's complement arithmetic, the three other
     * basic arithmetic operations of add, subtract, and multiply are
     * bit-wise identical if the two operands are regarded as both
     * being signed or both being unsigned. Therefore, separate {@code
     * addUnsigned}, etc. methods are not provided.
     *
     * @param int $dividend the value to be divided
     * @param int $divisor the value doing the dividing
     * @return int the unsigned quotient of the first argument divided by
     * the second argument
     * @see #remainderUnsigned
     */
    public static function divideUnsigned(int $dividend, int $divisor): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Returns the unsigned remainder from dividing the first argument
     * by the second where each argument and the result is interpreted
     * as an unsigned value.
     *
     * @param int $dividend the value to be divided
     * @param int $divisor the value doing the dividing
     * @return int the unsigned remainder of the first argument divided by
     * the second argument
     * @see #divideUnsigned
     */
    public static function remainderUnsigned(int $dividend, int $divisor): int {
        throw new UnsupportedOperationException();
    }


    // Bit twiddling

    /**
     * The number of bits used to represent an int value in two's
     * complement binary form.
     *
     * @since 1.5
     */
    public const SIZE = TInteger::BYTES * 8;

    /**
     * The number of bytes used to represent an int value in two's
     * complement binary form.
     *
     * @since 1.8
     */
    public const BYTES = PHP_INT_SIZE;

    /**
     * Implementation of Java's '>>>' operator / logical shift operator
     *
     *  Note: should work for both 32-bit and 64-bit integers
     *
     * @param int $value The value to shift
     * @param int $steps the amount of steps to take to the right
     *
     * @return int The shifted value
     */
    private static function unsignedRightShift(int $value, int $steps): int {
        $steps = $steps & (TInteger::SIZE - 1);
        if ($steps === 0) {
            return $value;
        }

        $mask = 1 << TInteger::SIZE - 2;
        if($value < 0)
        {
            $value &= TInteger::MAX_VALUE;
            $mask = $mask >> ($steps - 1);
            return ($value >> $steps) | $mask;
        }
        return $value >> $steps;
    }

    /**
     * Implementation of Java's '<<' operator / left shift operator, with full support for modulo
     *
     *  Note: should work for both 32-bit and 64-bit integers
     *
     * @param int $value The value to shift
     * @param int $steps the amount of steps to take to the right
     *
     * @return int The shifted value
     */
    private static function leftShift(int $value, int $steps): int {
        $steps = $steps & (TInteger::SIZE - 1);
        if ($steps === 0) {
            return $value;
        }

        return $value << $steps;
    }

    /**
     * Implementation of Java's '<<' operator / left shift operator, with full support for modulo
     *
     *  Note: should work for both 32-bit and 64-bit integers
     *
     * @param int $value The value to shift
     * @param int $steps the amount of steps to take to the right
     *
     * @return int The shifted value
     */
    private static function rightShift(int $value, int $steps): int {
        $steps = $steps & (TInteger::SIZE - 1);
        if ($steps === 0) {
            return $value;
        }

        return $value >> $steps;
    }

    /**
     * Implementation of Java's '-' operator / negate operator
     * Handles a special case for when the minimum value is used
     *
     *  Note: should work for both 32-bit and 64-bit integers
     *
     * @param int $value The value to negate
     *
     * @return int The negated value
     */
    private static function negate(int $value): int {
        if ($value === TInteger::MIN_VALUE) {
            return $value;
        }
        return -$value;
    }

    /**
     * Returns an int value with at most a single one-bit, in the
     * position of the highest-order ("leftmost") one-bit in the specified
     * int value.  Returns zero if the specified value has no
     * one-bits in its two's complement binary representation, that is, if it
     * is equal to zero.
     *
     * @param int $i the value whose highest one bit is to be computed
     * @return int an int value with a single one-bit, in the position
     *     of the highest-order one-bit in the specified value, or zero if
     *     the specified value is itself equal to zero.
     */
    public static function highestOneBit(int $i): int {
        // Based on: HD, Figure 3-1
        $i |= ($i >>  1);
        $i |= ($i >>  2);
        $i |= ($i >>  4);
        $i |= ($i >>  8);
        $i |= ($i >> 16);
        $i |= ($i >> 32);
        return $i - TInteger::unsignedRightShift($i, 1);
    }

    /**
     * Returns an int value with at most a single one-bit, in the
     * position of the lowest-order ("rightmost") one-bit in the specified
     * int value.  Returns zero if the specified value has no
     * one-bits in its two's complement binary representation, that is, if it
     * is equal to zero.
     *
     * @param int $i the value whose lowest one bit is to be computed
     * @return int an int value with a single one-bit, in the position
     *     of the lowest-order one-bit in the specified value, or zero if
     *     the specified value is itself equal to zero.
     */
    public static function lowestOneBit(int $i): int {
        // Based on: HD, Section 2-1
        return $i & TInteger::negate($i);
    }

    /**
     * Returns the number of zero bits preceding the highest-order
     * ("leftmost") one-bit in the two's complement binary representation
     * of the specified int value.  Returns 32 if the
     * specified value has no one-bits in its two's complement representation,
     * in other words if it is equal to zero.
     *
     * <p>Note that this method is closely related to the logarithm base 2.
     * For all positive int values x:
     * <ul>
     * <li>floor(log<sub>2</sub>(x)) = 31 - numberOfLeadingZeros(x)
     * <li>ceil(log<sub>2</sub>(x)) = 32 - numberOfLeadingZeros(x - 1)
     * </ul>
     *
     * @param int $i the value whose number of leading zeros is to be computed
     * @return int the number of zero bits preceding the highest-order
     *     ("leftmost") one-bit in the two's complement binary representation
     *     of the specified int value, or 32 if the value
     *     is equal to zero.
     */
    public static function numberOfLeadingZeros(int $i): int {
        // HD, Figure 5-6
        if ($i == 0) {
            return TInteger::SIZE;
        }
        $n = 1;
        $x = TInteger::unsignedRightShift($i, 32);
        if ($x === 0) {
            $n += 32;
            $x = $i;
        }
        if (TInteger::unsignedRightShift($x, 16) === 0) {
            $n += 16;
            $x <<= 16;
        }
        if (TInteger::unsignedRightShift($x, 24) === 0) {
            $n += 8;
            $x <<= 8;
        }
        if (TInteger::unsignedRightShift($x, 28) === 0) {
            $n += 4;
            $x <<= 4;
        }
        if (TInteger::unsignedRightShift($x, 30) === 0) {
            $n += 2;
            $x <<= 2;
        }
        $n -= TInteger::unsignedRightShift($x, 31);
        return $n;
    }

    /**
     * Returns the number of zero bits following the lowest-order ("rightmost")
     * one-bit in the two's complement binary representation of the specified
     * int value.  Returns 32 or 64 if the specified value has no
     * one-bits in its two's complement representation, in other words if it is
     * equal to zero.
     *
     *  Note: should work for both 32-bit and 64-bit integers
     *
     * @param int $i the value whose number of trailing zeros is to be computed
     *
     * @return int the number of zero bits following the lowest-order ("rightmost")
     *     one-bit in the two's complement binary representation of the
     *     specified int value, or 32 if the value is equal
     *     to zero.
     */
    public static function numberOfTrailingZeros(int $i): int {
        // Based on: HD, Figure 5-14
        if ($i == 0) {
            return self::SIZE;
        }

        $n = TInteger::SIZE - 1;
        for($step = TInteger::SIZE; $step >= 2; $step = $step / 2) {
            $y = $i << $step;
            if ($y != 0) {
                $n = $n - $step;
                $i = $y;
            }
        }

        return $n - TInteger::unsignedRightShift($i << 1, TInteger::SIZE - 1);
    }

    /**
     * Returns the number of one-bits in the two's complement binary
     * representation of the specified int value.  This function is
     * sometimes referred to as the <i>population count</i>.
     *
     * @param int $i the value whose bits are to be counted
     * @return int the number of one-bits in the two's complement binary
     *     representation of the specified int value.
     */
    public static function bitCount(int $i): int {
        // Based on: HD, Figure 5-14
        $i -= TInteger::unsignedRightShift($i, 1) & 0x5555555555555555;
        $i = ($i & 0x3333333333333333) + (TInteger::unsignedRightShift($i, 2) & 0x3333333333333333);
        $i = ($i + TInteger::unsignedRightShift($i, 4)) & 0x0f0f0f0f0f0f0f0f;
        $i += TInteger::unsignedRightShift($i, 8);
        $i += TInteger::unsignedRightShift($i, 16);
        $i += TInteger::unsignedRightShift($i, 32);
        return $i & 0x7f;
    }

    /**
     * Returns the value obtained by rotating the two's complement binary
     * representation of the specified int value left by the
     * specified number of bits.  (Bits shifted out of the left hand, or
     * high-order, side reenter on the right, or low-order.)
     *
     * <p>Note that left rotation with a negative distance is equivalent to
     * right rotation:
     * rotateLeft(val, -distance) == rotateRight(val, distance).
     * Note also that rotation by any multiple of 32 is a
     * no-op, so all but the last five bits of the rotation distance can be
     * ignored, even if the distance is negative:
     * rotateLeft(val, distance) == rotateLeft(val, distance & 0x1F).
     *
     *  Note: should work for both 32-bit and 64-bit integers
     *
     * @param int $i the value whose bits are to be rotated left
     * @param int $distance the number of bit positions to rotate left
     *
     * @return int the value obtained by rotating the two's complement binary
     *     representation of the specified int value left by the
     *     specified number of bits.
     */
    public static function rotateLeft(int $i, int $distance): int {
        return TInteger::leftShift($i, $distance) | TInteger::unsignedRightShift($i, TInteger::negate($distance));
    }

    /**
     * Returns the value obtained by rotating the two's complement binary
     * representation of the specified int value right by the
     * specified number of bits.  (Bits shifted out of the right hand, or
     * low-order, side reenter on the left, or high-order.)
     *
     * <p>Note that right rotation with a negative distance is equivalent to
     * left rotation:
     * rotateRight(val, -distance) == rotateLeft(val, distance).
     * Note also that rotation by any multiple of 32 is a
     * no-op, so all but the last five bits of the rotation distance can be
     * ignored, even if the distance is negative:
     * rotateRight(val, distance) == rotateRight(val, distance & 0x1F).
     *
     *  Note: should work for both 32-bit and 64-bit integers
     *
     * @param int $i the value whose bits are to be rotated right
     * @param int $distance the number of bit positions to rotate right
     *
     * @return int the value obtained by rotating the two's complement binary
     *     representation of the specified int value right by the
     *     specified number of bits.
     */
    public static function rotateRight(int $i, int $distance): int {
        return TInteger::unsignedRightShift($i,  $distance) | TInteger::leftShift($i, TInteger::negate($distance));
    }

    /**
     * Returns the value obtained by reversing the order of the bits in the
     * two's complement binary representation of the specified int
     * value.
     *
     * @param int $i the value to be reversed
     * @return int the value obtained by reversing order of the bits in the
     *     specified int value.
     */
    public static function reverse(int $i): int {
        // Based on: HD, Figure 7-1
        $i = ($i & 0x5555555555555555) << 1 | TInteger::unsignedRightShift($i, 1) & 0x5555555555555555;
        $i = ($i & 0x3333333333333333) << 2 | TInteger::unsignedRightShift($i, 2) & 0x3333333333333333;
        $i = ($i & 0x0f0f0f0f0f0f0f0f) << 4 | TInteger::unsignedRightShift($i, 4) & 0x0f0f0f0f0f0f0f0f;
        $i = ($i & 0x00ff00ff00ff00ff) << 8 | TInteger::unsignedRightShift($i, 8) & 0x00ff00ff00ff00ff;
        return ($i               << 48) |
              (($i & 0xffff0000) << 16) |
              (TInteger::unsignedRightShift($i, 16) & 0xffff0000) |
               TInteger::unsignedRightShift($i, 48);
    }

    /**
     * Returns the signum function of the specified int value.  (The
     * return value is -1 if the specified value is negative; 0 if the
     * specified value is zero; and 1 if the specified value is positive.)
     *
     *  Note: should work for both 32-bit and 64-bit integers
     *
     * @param int $i the value whose signum is to be computed
     *
     * @return int the signum function of the specified int value.
     */
    public static function signum(int $i): int {
        // Based on: HD, Section 2-7
        return ($i >> (TInteger::SIZE - 1)) | TInteger::unsignedRightShift(TInteger::negate($i), (TInteger::SIZE - 1));
    }

    /**
     * Returns the value obtained by reversing the order of the bytes in the
     * two's complement representation of the specified int value.
     *
     * @param int $i the value whose bytes are to be reversed
     * @return int the value obtained by reversing the bytes in the specified
     *     int value.
     */
    public static function reverseBytes(int $i): int {
        $i =    ($i & 0x00ff00ff00ff00ff) << 8    |
                TInteger::unsignedRightShift($i, 8)  & 0x00ff00ff00ff00ff;
        return (($i                       << 48)) |
               (($i & 0xffff0000)         << 16)  |
               (TInteger::unsignedRightShift($i, 16) & 0xffff0000) |
                TInteger::unsignedRightShift($i, 48);
    }

    /**
     * Adds two integers together as per the + operator, behaves like Java, with negative overflow.
     *
     *  Note: should work for both 32-bit and 64-bit integers
     *
     * @param int $a the first operand
     * @param int $b the second operand
     *
     * @return int the sum of a and b
     */
    public static function sum(int $a, int $b): int {
        $result = $a + $b;
        if (!GType::of($result)->isFloat()) {
            return $result;
        } elseif ($result > 0) {
            return TInteger::MIN_VALUE + ($b - (TInteger::MAX_VALUE - $a + 1));
        }
        return TInteger::MAX_VALUE + ($b - (TInteger::MIN_VALUE - $a - 1));
    }

    /**
     * Returns the greater of two int values
     * as if by calling {@link Math#max(int, int) Math.max}.
     *
     * Note: should work for both 32-bit and 64-bit integers
     *
     * @param int $a the first operand
     * @param int $b the second operand
     *
     * @return int the greatest of a and b
     */
    public static function max(int $a, int $b): int {
        return max($a, $b);
    }

    /**
     * Returns the smaller of two int values
     * as if by calling {@link Math#min(int, int) Math.min}.
     *
     * Note: should work for both 32-bit and 64-bit integers
     *
     * @param int $a the first operand
     * @param int $b the second operand
     * @return int the smallest of a and b
     */
    public static function min(int $a, int $b): int {
        return min($a, $b);
    }
}
