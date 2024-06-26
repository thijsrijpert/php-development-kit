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
 * Copyright (c) 2003, 2020, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */

namespace jhp\lang;

use Error;
use jhp\lang\exception\CloneNotSupportedException;
use jhp\lang\exception\IllegalArgumentException;

/**
 * This is the common base class of all JHP enumeration classes.
 */
trait EnumTrait
{

    /**
     * Returns the name of this enum constant, exactly as declared in its
     * enum declaration.
     *
     * <b>Most programmers should use the {@link #toString} method in
     * preference to this one, as the toString method may return
     * a more user-friendly name.</b>  This method is designed primarily for
     * use in specialized situations where correctness depends on getting the
     * exact name, which will not vary from release to release.
     *
     * @return string the name of this enum constant
     */
    final public function name(): string
    {
        return $this->name;
    }

    /**
     * Returns the ordinal of this enumeration constant (its position
     * in its enum declaration, where the initial constant is assigned
     * an ordinal of zero).
     *
     * Most programmers will have no use for this method.  It is
     * designed for use by sophisticated enum-based data structures, such
     * as {@link java.util.EnumSet} and {@link java.util.EnumMap}.
     *
     * @return int the ordinal of this enumeration constant
     */
    final public function ordinal(): int
    {
        foreach (self::cases() as $key => $value) {
            if ($value === $this) {
                return $key;
            }
        }

        return -1;
    }

    /**
     * Returns the name of this enum constant, as contained in the
     * declaration.  This method may be overridden, though it typically
     * isn't necessary or desirable.  An enum class should override this
     * method when a more "programmer-friendly" string form exists.
     *
     * @return string the name of this enum constant
     */
    public function toString(): string
    {
        return $this->name;
    }

    /**
     * Returns true if the specified object is equal to this
     * enum constant.
     *
     * @param ?IObject $other the object to be compared for equality with this object.
     *
     * @return  true if the specified object is equal to this
     *          enum constant.
     */
    final public function equals(?IObject $other = null): bool
    {
        if ($other === null) {
            return false;
        }
        return $this === $other;
    }

    /**
     * Returns the runtime class of this TObject.
     *
     * @return TClass The class object that represents the runtime class of this object.
     */
    public function getClass(): TClass
    {
        return TClass::of($this);
    }

    /**
     * Returns a hash code for this enum constant.
     *
     * @return int a hash code for this enum constant.
     */
    final public function hashCode(): int
    {
        return System::identityHashCode($this);
    }

    /**
     * Throws CloneNotSupportedException.  This guarantees that enums
     * are never cloned, which is necessary to preserve their "singleton"
     * status.
     *
     * @throws CloneNotSupportedException
     */
    public function clone(): IObject
    {
        throw new CloneNotSupportedException();
    }


    /**
     * Compares this enum with the specified object for order.  Returns a
     * negative integer, zero, or a positive integer as this object is less
     * than, equal to, or greater than the specified object.
     *
     * Enum constants are only comparable to other enum constants of the
     * same enum type.  The natural order implemented by this
     * method is the order in which the constants are declared.
     */
    final public function compareTo(IObject $obj): int
    {
        if ($obj instanceof IEnum && $this->getClass()->equals($obj->getClass())) {
            return $this->ordinal() - $obj->ordinal();
        }

        throw new IllegalArgumentException("Compare is done on object of different type");
    }

    /**
     * The same as getClass()
     *
     * @return TClass the Class object corresponding to this enum constant's
     *     enum type
     */
    final public function getDeclaringClass(): TClass
    {
        return $this->getClass();
    }

    /**
     * Returns the enum constant of the specified enum class with the
     * specified name.  The name must match exactly an identifier used
     * to declare an enum constant in this class.  (Extraneous whitespace
     * characters are not permitted.
     *
     * @param TClass $enumClass the {@code Class} object of the enum class from which to return a constant
     * @param string $name the name of the constant to return
     *
     * @return IEnum the enum constant of the specified enum class with the specified name
     *
     * @throws IllegalArgumentException if the specified enum class has
     *         no constant with the specified name, or the specified
     *         class object does not represent an enum class
     */
    public static function valueOf(TClass $enumClass, string $name): IEnum
    {
        $enumClassName = $enumClass->getName();
        try {
            $result = constant( "$enumClassName::$name");
        } catch (Error) {
            throw new IllegalArgumentException("No enum constant " . $enumClass->getName() . "." . $name);
        }

        if ($result === null) {
            throw new IllegalArgumentException("No enum constant " . $enumClass->getName() . "." . $name);
        }

        return $result;
    }

    /**
     * Causes the current thread to be paused for the supplied timeout
     *
     * @param int $timeout Timeout in milliseconds
     * @param int $nanos Timeout in nanoseconds (Not really used)
     */
    final public function wait(int $timeout = 0, int $nanos = 0): void
    {
        if ($timeout < 0) {
            throw new IllegalArgumentException("timeout value is negative");
        }

        if ($nanos < 0 || $nanos > 999999) {
            throw new IllegalArgumentException("nanosecond timeout value out of range");
        }

        if ($nanos > 0 && $timeout < TInteger::MAX_VALUE) {
            $timeout++;
        }

        usleep($timeout * 1000);
    }
}