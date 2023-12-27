<?php

namespace jhp\lang;

use jhp\util\function\internal\NullPointerException;

/**
 * The Boolean class wraps a value of the primitive type
 * {@code boolean} in an object. An object of type
 * {@code Boolean} contains a single field whose type is
 * {@code boolean}.
 *
 * <p>In addition, this class provides many methods for
 * converting a {@code boolean} to a {@code String} and a
 * {@code String} to a {@code boolean}, as well as other
 * constants and methods useful when dealing with a
 * {@code boolean}.
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
class TBoolean
{

    private function __construct(private readonly bool $value) {

    }

    /**
     * Parses the string argument as a boolean.  The {@code boolean}
     * returned represents the value {@code true} if the string argument
     * is not {@code null} and is equal, ignoring case, to the string
     * {@code "true"}.
     * Otherwise, a false value is returned, including for a null
     * argument.<p>
     * Example: {@code Boolean.parseBoolean("True")} returns {@code true}.<br>
     * Example: {@code Boolean.parseBoolean("yes")} returns {@code false}.
     *
     * @param      s   the {@code String} containing the boolean
     *                 representation to be parsed
     * @return     the boolean represented by the string argument
     * @since 1.5
     */
    public static function parseBoolean(String $s): bool {
        return strtolower($s) === "true";
    }

    /**
     * Returns the value of this {@code Boolean} object as a boolean
     * primitive.
     *
     * @return  the primitive {@code boolean} value of this object.
     */
    public function booleanValue(): bool {
        return $this->value;
    }

    /**
     * Returns a {@code Boolean} instance representing the specified
     * {@code boolean} value.  If the specified {@code boolean} value
     * is {@code true}, this method returns {@code Boolean.TRUE};
     * if it is {@code false}, this method returns {@code Boolean.FALSE}.
     * If a new {@code Boolean} instance is not required, this method
     * should generally be used in preference to the constructor
     * {@link #Boolean(boolean)}, as this method is likely to yield
     * significantly better space and time performance.
     *
     * @param  b a boolean value.
     * @return a {@code Boolean} instance representing {@code b}.
     * @since  1.4
     */
    public static function valueOf(bool|String $b): TBoolean {
        if ($b instanceof String) {
            $b = self::parseBoolean($b);
        }
        return $b ? new TBoolean(true) : new TBoolean(false);
    }

    /**
     * Returns a {@code String} object representing the specified
     * boolean.  If the specified boolean is {@code true}, then
     * the string {@code "true"} will be returned, otherwise the
     * string {@code "false"} will be returned.
     *
     * @param b the boolean to be converted
     * @return the string representation of the specified {@code boolean}
     * @since 1.4
     */
    public static function toString(bool $b): string {
        return $b ? "true" : "false";
    }

    /**
     * Returns {@code true} if and only if the argument is not
     * {@code null} and is a {@code Boolean} object that
     * represents the same {@code boolean} value as this object.
     *
     * @param   obj   the object to compare with.
     * @return  {@code true} if the Boolean objects represent the
     *          same value; {@code false} otherwise.
     */
    public function equals(Object $obj): bool {
        if ($obj instanceof TBoolean) {
            return $this->value == ($obj->booleanValue());
        }
        return false;
    }

    /**
     * Compares this {@code Boolean} instance with another.
     *
     * @param   b the {@code Boolean} instance to be compared
     * @return  zero if this object represents the same boolean value as the
     *          argument; a positive value if this object represents true
     *          and the argument represents false; and a negative value if
     *          this object represents false and the argument represents true
     * @throws  NullPointerException if the argument is {@code null}
     * @see     Comparable
     * @since  1.5
     */
    public function compareTo(TBoolean $b): int {
        return TBoolean::compare($this->value, $b->value);
    }

    /**
     * Compares two {@code boolean} values.
     * The value returned is identical to what would be returned by:
     * <pre>
     *    Boolean.valueOf(x).compareTo(Boolean.valueOf(y))
     * </pre>
     *
     * @param  x the first {@code boolean} to compare
     * @param  y the second {@code boolean} to compare
     * @return the value {@code 0} if {@code x == y};
     *         a value less than {@code 0} if {@code !x && y}; and
     *         a value greater than {@code 0} if {@code x && !y}
     * @since 1.7
     */
    public static function compare(bool $x, bool $y): int {
        return ($x == $y) ? 0 : ($x ? 1 : -1);
    }

    /**
     * Returns the result of applying the logical AND operator to the
     * specified {@code boolean} operands.
     *
     * @param a the first operand
     * @param b the second operand
     * @return the logical AND of {@code a} and {@code b}
     * @see java.util.function.BinaryOperator
     * @since 1.8
     */
    public static function logicalAnd(bool $a, bool $b): bool {
        return $a && $b;
    }

    /**
     * Returns the result of applying the logical OR operator to the
     * specified {@code boolean} operands.
     *
     * @param a the first operand
     * @param b the second operand
     * @return the logical OR of {@code a} and {@code b}
     * @see java.util.function.BinaryOperator
     * @since 1.8
     */
    public static function logicalOr(bool $a, bool $b): bool {
        return $a || $b;
    }

    /**
     * Returns the result of applying the logical XOR operator to the
     * specified {@code boolean} operands.
     *
     * @param a the first operand
     * @param b the second operand
     * @return  the logical XOR of {@code a} and {@code b}
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