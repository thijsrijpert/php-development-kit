<?php

namespace jhp\lang;

use jhp\util\function\internal\NullPointerException;

/**
 * The Boolean class wraps a value of the primitive type
 * boolean in an object. An object of type
 * Boolean contains a single field whose type is
 * boolean.
 *
 * <p>In addition, this class provides many methods for
 * converting a boolean to a String and a
 * String to a boolean, as well as other
 * constants and methods useful when dealing with a
 * boolean.
 *
 * <p>This is a <a href="{@docRoot}/java.base/java/lang/doc-files/ValueBased.html">value-based</a>
 * class; programmers should treat instances that are
 * {@linkplain #equals(Object) equal} as interchangeable and should not
 * use instances for synchronization, or unpredictable behavior may
 * occur. For example, in a future release, synchronization may fail.
 *
 * @author  Arthur van Hoff
 * @since   1.0
 */
class TBoolean extends TObject
{

    private function __construct(private readonly bool $value) { }

    /**
     * Parses the string argument as a boolean.  The boolean
     * returned represents the value true if the string argument
     * is not null and is equal, ignoring case, to the string
     * "true".
     * Otherwise, a false value is returned, including for a null
     * argument.<p>
     * Example: Boolean.parseBoolean("True") returns true.<br>
     * Example: Boolean.parseBoolean("yes") returns false.
     *
     * @param      s   the String containing the boolean
     *                 representation to be parsed
     * @return     the boolean represented by the string argument
     * @since 1.5
     */
    public static function parseBoolean(String $s): bool {
        return strtolower($s) === "true";
    }

    /**
     * Returns the value of this Boolean object as a boolean
     * primitive.
     *
     * @return  the primitive boolean value of this object.
     */
    public function booleanValue(): bool {
        return $this->value;
    }

    /**
     * Returns a Boolean instance representing the specified
     * boolean value.  If the specified boolean value
     * is true, this method returns Boolean.TRUE;
     * if it is false, this method returns Boolean.FALSE.
     * If a new Boolean instance is not required, this method
     * should generally be used in preference to the constructor
     * {@link #Boolean(boolean)}, as this method is likely to yield
     * significantly better space and time performance.
     *
     * @param  b a boolean value.
     * @return a Boolean instance representing b.
     * @since  1.4
     */
    public static function valueOf(bool|String $b): TBoolean {
        if ($b instanceof String) {
            $b = self::parseBoolean($b);
        }
        return $b ? new TBoolean(true) : new TBoolean(false);
    }

    /**
     * Returns a String object representing the specified
     * boolean.  If the specified boolean is true, then
     * the string "true" will be returned, otherwise the
     * string "false" will be returned.
     *
     * @param b the boolean to be converted
     * @return the string representation of the specified boolean
     * @since 1.4
     */
    public static function asString(bool $b): string {
        return $b ? "true" : "false";
    }

    /**
     * Returns a hash code for this Boolean object.
     *
     * @return  the integer 1231 if this object represents
     * true; returns the integer 1237 if this
     * object represents false.
     */
    public function hashCode(): int {
        return TBoolean::asHashCode($this->value);
    }

    /**
     * Returns a hash code for a boolean value; compatible with
     * Boolean.hashCode().
     *
     * @param value the value to hash
     * @return a hash code value for a boolean value.
     * @since 1.8
     */
    public static function asHashCode(bool $value): int {
        return $value ? 1231 : 1237;
    }

    /**
     * Returns true if and only if the argument is not
     * null and is a Boolean object that
     * represents the same boolean value as this object.
     *
     * @param   obj   the object to compare with.
     * @return  true if the Boolean objects represent the
     *          same value; false otherwise.
     */
    public function equals(Object $obj): bool {
        if ($obj instanceof TBoolean) {
            return $this->value == ($obj->booleanValue());
        }
        return false;
    }

    /**
     * Compares this Boolean instance with another.
     *
     * @param   b the Boolean instance to be compared
     * @return  zero if this object represents the same boolean value as the
     *          argument; a positive value if this object represents true
     *          and the argument represents false; and a negative value if
     *          this object represents false and the argument represents true
     * @throws  NullPointerException if the argument is null
     * @see     Comparable
     * @since  1.5
     */
    public function compareTo(TBoolean $b): int {
        return TBoolean::compare($this->value, $b->value);
    }

    /**
     * Compares two boolean values.
     * The value returned is identical to what would be returned by:
     * <pre>
     *    Boolean.valueOf(x).compareTo(Boolean.valueOf(y))
     * </pre>
     *
     * @param  x the first boolean to compare
     * @param  y the second boolean to compare
     * @return the value 0 if x == y;
     *         a value less than 0 if !x && y; and
     *         a value greater than 0 if x && !y
     * @since 1.7
     */
    public static function compare(bool $x, bool $y): int {
        return ($x == $y) ? 0 : ($x ? 1 : -1);
    }

    /**
     * Returns the result of applying the logical AND operator to the
     * specified boolean operands.
     *
     * @param a the first operand
     * @param b the second operand
     * @return the logical AND of a and b
     * @see java.util.function.BinaryOperator
     * @since 1.8
     */
    public static function logicalAnd(bool $a, bool $b): bool {
        return $a && $b;
    }

    /**
     * Returns the result of applying the logical OR operator to the
     * specified boolean operands.
     *
     * @param a the first operand
     * @param b the second operand
     * @return the logical OR of a and b
     * @see java.util.function.BinaryOperator
     * @since 1.8
     */
    public static function logicalOr(bool $a, bool $b): bool {
        return $a || $b;
    }

    /**
     * Returns the result of applying the logical XOR operator to the
     * specified boolean operands.
     *
     * @param a the first operand
     * @param b the second operand
     * @return  the logical XOR of a and b
     * @see java.util.function.BinaryOperator
     * @since 1.8
     */
    public static function logicalXor(bool $a, bool $b): bool {
        return $a ^ $b;
    }

    /**
     * Returns an {@link Optional} containing the nominal descriptor for this
     * instance.
     *
     * @return an {@link Optional} describing the {@linkplain Boolean} instance
     * @since 15
     */
    public function describeConstable(): Optional {
        throw new NullPointerException();
    }

}