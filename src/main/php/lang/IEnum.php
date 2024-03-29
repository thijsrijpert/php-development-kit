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

use jhp\io\Serializable;

interface IEnum extends IObject, Comparable, Serializable
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
     * @return the name of this enum constant
     */
    function name(): string;

    /**
     * Returns the ordinal of this enumeration constant (its position
     * in its enum declaration, where the initial constant is assigned
     * an ordinal of zero).
     *
     * Most programmers will have no use for this method.  It is
     * designed for use by sophisticated enum-based data structures, such
     * as {@link java.util.EnumSet} and {@link java.util.EnumMap}.
     *
     * @return the ordinal of this enumeration constant
     */
    function ordinal(): int;

    /**
     * Returns the Class object corresponding to this enum constant's
     * enum type.  Two enum constants e1 and  e2 are of the
     * same enum type if and only if
     *   e1.getDeclaringClass() == e2.getDeclaringClass().
     * (The value returned by this method may differ from the one returned
     * by the {@link Object#getClass} method for enum constants with
     * constant-specific class bodies.)
     *
     * @return the Class object corresponding to this enum constant's
     *     enum type
     */
    function getDeclaringClass(): TClass;

    /**
     * Returns the enum constant of the specified enum class with the
     * specified name.  The name must match exactly an identifier used
     * to declare an enum constant in this class.  (Extraneous whitespace
     * characters are not permitted.)
     *
     * <p>Note that for a particular enum class {@code T}, the
     * implicitly declared {@code public static T valueOf(String)}
     * method on that enum may be used instead of this method to map
     * from a name to the corresponding enum constant.  All the
     * constants of an enum class can be obtained by calling the
     * implicit {@code public static T[] values()} method of that
     * class.
     *
     * @param <T> The enum class whose constant is to be returned
     * @param enumClass the {@code Class} object of the enum class from which
     *      to return a constant
     * @param name the name of the constant to return
     * @return the enum constant of the specified enum class with the
     *      specified name
     * @throws IllegalArgumentException if the specified enum class has
     *         no constant with the specified name, or the specified
     *         class object does not represent an enum class
     * @throws NullPointerException if {@code enumClass} or {@code name}
     *         is null
     * @since 1.5
     */
    public static function valueOf(TClass $enumClass, String $name): IEnum;
}
