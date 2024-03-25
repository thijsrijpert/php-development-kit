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
 * Copyright (c) 1994, 2020, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */

namespace jhp\lang;

use jhp\io\Serializable;
use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\internal\GType;

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
class TBoolean extends TObject implements Serializable, Comparable
{

    /**
     * Create a new boolean wrapper object
     * @param bool $value the value being wrapped
     */
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
     * @param      string $s   the String containing the boolean
     *                 representation to be parsed
     * @return     bool the boolean represented by the string argument
     * @since 1.5
     */
    public static function parseBoolean(string $s): bool {
        return strtolower($s) === "true";
    }

    /**
     * Returns the value of this Boolean object as a boolean
     * primitive.
     *
     * @return  bool the primitive boolean value of this object.
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
     * @param  bool|string $b a boolean value.
     * @return TBoolean a Boolean instance representing b.
     */
    public static function valueOf(bool|string $b): TBoolean {
        if (GType::of($b)->isString()) {
            $b = self::parseBoolean($b);
        }
        return $b ? new TBoolean(true) : new TBoolean(false);
    }

    /**
     * @apiNote
     * In general, the toString method returns a string that
     * "textually represents" this object. The result should
     * be a concise but informative representation that is easy for a
     * person to read.
     * It is recommended that all subclasses override this method.
     * The string output is not necessarily stable over time or across
     * invocations.
     *
     * The toString method for class Object
     * returns a string consisting of the name of the class of which the
     * object is an instance, the at-sign character '@', and
     * the unsigned hexadecimal representation of the hash code of the
     * object.
     *
     * @return  string a string representation of the object.
     * @see TClass::getName()
     * @see TObject::getClass()
     * @see TInteger::toHexString()
     * @see TObject::hashCode()
     */
    public function toString(): string {
        return TBoolean::asString($this->value);
    }

    /**
     * Returns a String object representing the specified
     * boolean.  If the specified boolean is true, then
     * the string "true" will be returned, otherwise the
     * string "false" will be returned.
     *
     * @param bool $b the boolean to be converted
     * @return string the string representation of the specified boolean
     * @since 1.4
     */
    public static function asString(bool $b): string {
        return $b ? "true" : "false";
    }

    /**
     * Returns a hash code for this Boolean object.
     *
     * @return int the integer 1231 if this object represents
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
     * @param bool $value the value to hash
     * @return int a hash code value for a boolean value.
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
     * @param   ?IObject $obj   the object to compare with.
     * @return  bool true if the Boolean objects represent the
     *          same value; false otherwise.
     */
    public function equals(?IObject $obj = null): bool {
        if ($obj === null) {
            return false;
        }
        if ($obj instanceof TBoolean) {
            return $this->value == ($obj->booleanValue());
        }
        return false;
    }

    /**
     * Compares this Boolean instance with another.
     *
     * @param   TBoolean $o the Boolean instance to be compared
     * @return  int zero if this object represents the same boolean value as the
     *          argument; a positive value if this object represents true
     *          and the argument represents false; and a negative value if
     *          this object represents false and the argument represents true
     * @see     Comparable
     */
    public function compareTo(IObject $o): int {
        if ($o instanceof TBoolean) {
            return TBoolean::compare($this->value, $o->value);
        }

        throw new IllegalArgumentException("Cannot compare TBoolean with: " . TClass::of($o)->getName());
    }

    /**
     * Compares two boolean values.
     * The value returned is identical to what would be returned by:
     * <pre>
     *    Boolean.valueOf(x).compareTo(Boolean.valueOf(y))
     * </pre>
     *
     * @param  bool $x the first boolean to compare
     * @param  bool $y the second boolean to compare
     * @return int the value 0 if x == y;
     *         a value less than 0 if !x && y; and
     *         a value greater than 0 if x && !y
     */
    public static function compare(bool $x, bool $y): int {
        return ($x == $y) ? 0 : ($x ? 1 : -1);
    }

    /**
     * Returns the result of applying the logical AND operator to the
     * specified boolean operands.
     *
     * @param bool $a the first operand
     * @param bool $b the second operand
     * @return bool the logical AND of a and b
     */
    public static function logicalAnd(bool $a, bool $b): bool {
        return $a && $b;
    }

    /**
     * Returns the result of applying the logical OR operator to the
     * specified boolean operands.
     *
     * @param bool $a the first operand
     * @param bool $b the second operand
     * @return bool the logical OR of a and b
     */
    public static function logicalOr(bool $a, bool $b): bool {
        return $a || $b;
    }

    /**
     * Returns the result of applying the logical XOR operator to the
     * specified boolean operands.
     *
     * @param bool $a the first operand
     * @param bool $b the second operand
     * @return bool the logical XOR of a and b
     */
    public static function logicalXor(bool $a, bool $b): bool {
        return $a ^ $b;
    }
}