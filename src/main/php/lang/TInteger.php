<?php
namespace jhp\lang;

use jhp\lang\exception\NumberFormatException;
use jhp\lang\exception\UnsupportedOperationException;
use TypeError;

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
    private const digits = [
        '0' => 0 , '1' => 1 , '2' => 2 , '3' => 3, '4' => 4 , '5' => 5,
        '6' => 6, '7' => 7, '8' => 8, '9' => 9, 'a' => 10, 'b' => 11,
        'c' => 12, 'd' => 13, 'e' => 14, 'f' => 15, 'g' => 16, 'h' => 17,
        'i' => 18, 'j' => 19, 'k' => 20, 'l' => 21, 'm' => 22, 'n' => 23,
        'o' => 24, 'p' => 25, 'q' => 26, 'r' => 27, 's' => 28, 't' => 29,
        'u' => 30, 'v' => 31, 'w' => 32, 'x' => 33, 'y' => 34, 'z' => 35
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
        throw new UnsupportedOperationException();
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
     * @see #parseUnsignedInt(String, int)
     * @see #toUnsignedString(int, int)
     * @since   JDK1.0.2
     */
    public static function toHexString(int $i): string {
        throw new UnsupportedOperationException();
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
     * @see #parseUnsignedInt(String, int)
     * @see #toUnsignedString(int, int)
     * @since   JDK1.0.2
     */
    public static function toOctalString(int $i): string {
        throw new UnsupportedOperationException();
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
     * @see #parseUnsignedInt(String, int)
     * @see #toUnsignedString(int, int)
     * @since   JDK1.0.2
     */
    public static function toBinaryString(int $i): string
    {
        throw new UnsupportedOperationException();
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
     * parseInt("2147483648", 10) is unstable, uses floatmath becomes inaccorate
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
            if (self::digits[$s[$i]] >= $radix) {
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
     * java.lang.Character#digit(char, int)} returns a nonnegative
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
     * Returns the value of this Integer as a byte
     * after a narrowing primitive conversion.
     */
    public function byteValue(): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Returns the value of this Integer as a short
     * after a narrowing primitive conversion.
     */
    public function shortValue(): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Returns the value of this Integer as an
     * int.
     */
    public function intValue(): int {
        return $this->value;
    }

    /**
     * Returns the value of this Integer as a long
     * after a widening primitive conversion.
     */
    public function longValue(): int {
        throw new UnsupportedOperationException();
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
     * Returns the value of this Integer as a double
     * after a widening primitive conversion.
     */
    public function doubleValue(): float {
        throw new UnsupportedOperationException();
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
     * Determines the integer value of the system property with the
     * specified name.
     *
     * <p>The first argument is treated as the name of a system
     * property.  System properties are accessible through the {@link
     * java.lang.System#getProperty(java.lang.String)} method. The
     * string value of this property is then interpreted as an integer
     * value using the grammar supported by {@link Integer#decode decode} and
     * an Integer object representing this value is returned.
     *
     * <p>The second argument is the default value. An Integer object
     * that represents the value of the second argument is returned if there
     * is no property of the specified name, if the property does not have
     * the correct numeric format, or if the specified name is empty or
     * null.
     *
     * <p>In other words, this method returns an Integer object
     * equal to the value of:
     *
     * <blockquote>
     *  getInteger(nm, new Integer(val))
     * </blockquote>
     *
     * but in practice it may be implemented in a manner such as:
     *
     * <blockquote><pre>
     * Integer result = getInteger(nm, null);
     * return (result == null) ? new Integer(val) : result;
     * </pre></blockquote>
     *
     * to avoid the unnecessary allocation of an Integer
     * object when the default value is not needed.
     *
     * @param string            $nm
     * @param int|TInteger|null $val
     *
     * @return TInteger Integer value of the property.
     * @see     java.lang.System#getProperty(java.lang.String)
     * @see     java.lang.System#getProperty(java.lang.String, java.lang.String)
     */
    public static function getInteger(string $nm, int|TInteger|null $val = null): TInteger {
        throw new UnsupportedOperationException();
    }

    /**
     * Decodes a String into an Integer.
     * Accepts decimal, hexadecimal, and octal numbers given
     * by the following grammar:
     *
     * <blockquote>
     * <dl>
     * <dt><i>DecodableString:</i>
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
        } else if ($nm[0] == '+') {
            $index++;
        }



        // Handle radix specifier, if present
        if (substr($nm, $index, 2) === "0x" || substr($nm, $index, 2) === "0X" ) {
            $index += 2;
            $radix = 16;
        } else if (substr($nm, $index, 1) === "#") {
            $index++;
            $radix = 16;
        } else if (substr($nm, $index, 1) === "0" && strlen($nm) > 1 + $index) {
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
     * @param   anotherInteger   the Integer to be compared.
     * @return  the value 0 if this Integer is
     *          equal to the argument Integer; a value less than
     *          0 if this Integer is numerically less
     *          than the argument Integer; and a value greater
     *          than 0 if this Integer is numerically
     *           greater than the argument Integer (signed
     *           comparison).
     * @since   1.2
     */
    public function compareTo(object $o): int {
        if ($o instanceof TInteger) {
            return $this->value <=> $o->value;
        }

        throw new TypeError("Trying to compare Integer with: " . TClass::of($o)->getName());
    }

    /**
     * Compares two int values numerically.
     * The value returned is identical to what would be returned by:
     * <pre>
     *    Integer.valueOf(x).compareTo(Integer.valueOf(y))
     * </pre>
     *
     * @param  x the first int to compare
     * @param  y the second int to compare
     * @return the value 0 if x == y;
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
     * @param  x the first int to compare
     * @param  y the second int to compare
     * @return the value 0 if x == y; a value less
     *         than 0 if x < y as unsigned values; and
     *         a value greater than 0 if x > y as
     *         unsigned values
     * @since 1.8
     */
    public static function compareUnsigned(int $x, int $y): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Converts the argument to a long by an unsigned
     * conversion.  In an unsigned conversion to a long, the
     * high-order 32 bits of the long are zero and the
     * low-order 32 bits are equal to the bits of the integer
     * argument.
     *
     * Consequently, zero and positive int values are mapped
     * to a numerically equal long value and negative {@code
     * int} values are mapped to a long value equal to the
     * input plus 2<sup>32</sup>.
     *
     * @param  x the value to convert to an unsigned long
     * @return the argument converted to long by an unsigned
     *         conversion
     * @since 1.8
     */
    public static function toUnsignedLong(int $x): int {
        throw new UnsupportedOperationException();
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
     * @param dividend the value to be divided
     * @param divisor the value doing the dividing
     * @return the unsigned quotient of the first argument divided by
     * the second argument
     * @see #remainderUnsigned
     * @since 1.8
     */
    public static function divideUnsigned(int $dividend, int $divisor): int {
        /* See Hacker's Delight (2nd ed), section 9.3 */
        if ($divisor >= 0) {
            $q = (($dividend >> 1) * -1) / $divisor << 1;
            $r = $dividend - $q * $divisor;
            return $q + ((($r | ~($r - $divisor)) >> (TInteger::SIZE - 1)) * -1);
        }
        return (($dividend & ~($dividend - $divisor)) >> (TInteger::SIZE - 1))  * -1;
    }

    /**
     * Returns the unsigned remainder from dividing the first argument
     * by the second where each argument and the result is interpreted
     * as an unsigned value.
     *
     * @param dividend the value to be divided
     * @param divisor the value doing the dividing
     * @return the unsigned remainder of the first argument divided by
     * the second argument
     * @see #divideUnsigned
     * @since 1.8
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
     * The number of bytes used to represent a int value in two's
     * complement binary form.
     *
     * @since 1.8
     */
    public const BYTES = PHP_INT_SIZE;

    /**
     * Returns an int value with at most a single one-bit, in the
     * position of the highest-order ("leftmost") one-bit in the specified
     * int value.  Returns zero if the specified value has no
     * one-bits in its two's complement binary representation, that is, if it
     * is equal to zero.
     *
     * @param i the value whose highest one bit is to be computed
     * @return an int value with a single one-bit, in the position
     *     of the highest-order one-bit in the specified value, or zero if
     *     the specified value is itself equal to zero.
     * @since 1.5
     */
    public static function highestOneBit(int $i) {
        throw new UnsupportedOperationException();
    }

    /**
     * Returns an int value with at most a single one-bit, in the
     * position of the lowest-order ("rightmost") one-bit in the specified
     * int value.  Returns zero if the specified value has no
     * one-bits in its two's complement binary representation, that is, if it
     * is equal to zero.
     *
     * @param i the value whose lowest one bit is to be computed
     * @return an int value with a single one-bit, in the position
     *     of the lowest-order one-bit in the specified value, or zero if
     *     the specified value is itself equal to zero.
     * @since 1.5
     */
    public static function lowestOneBit(int $i): int {
        throw new UnsupportedOperationException();
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
     * @param i the value whose number of leading zeros is to be computed
     * @return the number of zero bits preceding the highest-order
     *     ("leftmost") one-bit in the two's complement binary representation
     *     of the specified int value, or 32 if the value
     *     is equal to zero.
     * @since 1.5
     */
    public static function numberOfLeadingZeros(int $i): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Returns the number of zero bits following the lowest-order ("rightmost")
     * one-bit in the two's complement binary representation of the specified
     * int value.  Returns 32 if the specified value has no
     * one-bits in its two's complement representation, in other words if it is
     * equal to zero.
     *
     * @param i the value whose number of trailing zeros is to be computed
     * @return the number of zero bits following the lowest-order ("rightmost")
     *     one-bit in the two's complement binary representation of the
     *     specified int value, or 32 if the value is equal
     *     to zero.
     * @since 1.5
     */
    public static function numberOfTrailingZeros(int $i): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Returns the number of one-bits in the two's complement binary
     * representation of the specified int value.  This function is
     * sometimes referred to as the <i>population count</i>.
     *
     * @param i the value whose bits are to be counted
     * @return the number of one-bits in the two's complement binary
     *     representation of the specified int value.
     * @since 1.5
     */
    public static function bitCount(int $i): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Returns the value obtained by rotating the two's complement binary
     * representation of the specified int value left by the
     * specified number of bits.  (Bits shifted out of the left hand, or
     * high-order, side reenter on the right, or low-order.)
     *
     * <p>Note that left rotation with a negative distance is equivalent to
     * right rotation: {@code rotateLeft(val, -distance) == rotateRight(val,
     * distance)}.  Note also that rotation by any multiple of 32 is a
     * no-op, so all but the last five bits of the rotation distance can be
     * ignored, even if the distance is negative: {@code rotateLeft(val,
     * distance) == rotateLeft(val, distance & 0x1F)}.
     *
     * @param i the value whose bits are to be rotated left
     * @param distance the number of bit positions to rotate left
     * @return the value obtained by rotating the two's complement binary
     *     representation of the specified int value left by the
     *     specified number of bits.
     * @since 1.5
     */
    public static function rotateLeft(int $i, int $distance): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Returns the value obtained by rotating the two's complement binary
     * representation of the specified int value right by the
     * specified number of bits.  (Bits shifted out of the right hand, or
     * low-order, side reenter on the left, or high-order.)
     *
     * <p>Note that right rotation with a negative distance is equivalent to
     * left rotation: {@code rotateRight(val, -distance) == rotateLeft(val,
     * distance)}.  Note also that rotation by any multiple of 32 is a
     * no-op, so all but the last five bits of the rotation distance can be
     * ignored, even if the distance is negative: {@code rotateRight(val,
     * distance) == rotateRight(val, distance & 0x1F)}.
     *
     * @param i the value whose bits are to be rotated right
     * @param distance the number of bit positions to rotate right
     * @return the value obtained by rotating the two's complement binary
     *     representation of the specified int value right by the
     *     specified number of bits.
     * @since 1.5
     */
    public static function rotateRight(int $i, int $distance): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Returns the value obtained by reversing the order of the bits in the
     * two's complement binary representation of the specified int
     * value.
     *
     * @param i the value to be reversed
     * @return the value obtained by reversing order of the bits in the
     *     specified int value.
     * @since 1.5
     */
    public static function reverse(int $i): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Returns the signum function of the specified int value.  (The
     * return value is -1 if the specified value is negative; 0 if the
     * specified value is zero; and 1 if the specified value is positive.)
     *
     * @param i the value whose signum is to be computed
     * @return the signum function of the specified int value.
     * @since 1.5
     */
    public static function signum(int $i): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Returns the value obtained by reversing the order of the bytes in the
     * two's complement representation of the specified int value.
     *
     * @param i the value whose bytes are to be reversed
     * @return the value obtained by reversing the bytes in the specified
     *     int value.
     * @since 1.5
     */
    public static function reverseBytes(int $i): int {
        throw new UnsupportedOperationException();
    }

    /**
     * Adds two integers together as per the + operator.
     *
     * @param a the first operand
     * @param b the second operand
     * @return the sum of a and b
     * @see java.util.function.BinaryOperator
     * @since 1.8
     */
    public static function sum(int $a, int $b): int {
        return $a + $b;
    }

    /**
     * Returns the greater of two int values
     * as if by calling {@link Math#max(int, int) Math.max}.
     *
     * @param a the first operand
     * @param b the second operand
     * @return the greater of a and b
     * @see java.util.function.BinaryOperator
     * @since 1.8
     */
    public static function max(int $a, int $b): int {
        return max($a, $b);
    }

    /**
     * Returns the smaller of two int values
     * as if by calling {@link Math#min(int, int) Math.min}.
     *
     * @param a the first operand
     * @param b the second operand
     * @return the smaller of a and b
     * @see java.util.function.BinaryOperator
     * @since 1.8
     */
    public static function min(int $a, int $b): int {
        return min($a, $b);
    }
}
