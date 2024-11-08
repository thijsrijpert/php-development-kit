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
 * Copyright (c) 1997, 2021, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */

namespace jhp\lang;

use jhp\lang\exception\IllegalArgumentException;
use jhp\util\collection\Comparator;

/**
 * This interface imposes a total ordering on the objects of each class that
 * implements it.  This ordering is referred to as the class's <i>natural
 * ordering</i>, and the class's compareTo method is referred to as
 * its <i>natural comparison method</i>.<p>
 *
 * Lists (and arrays) of objects that implement this interface can be sorted
 * automatically by {@link Collections::sort()} (and {@link Arrays::sort() }).
 * Objects that implement this interface can be used as keys in a {@link SortedMap} or as
 * elements in a {@link  SortedSet}, without the need to
 * specify a {@link Comparator}.<p>
 *
 * The natural ordering for a class C is said to be <i>consistent
 * with equals</i> if and only if 1.compareTo(e2) == 0 has
 * the same boolean value as e1.equals(e2) for every e1 and e2 of class C.
 * It is strongly recommended (though not required) that natural orderings be
 * consistent with equals.  This is so because sorted sets (and sorted maps)
 * without explicit comparators behave "strangely" when they are used with
 * elements (or keys) whose natural ordering is inconsistent with equals.  In
 * particular, such a sorted set (or sorted map) violates the general contract
 * for set (or map), which is defined in terms of the equals method.<p>
 *
 * For example, if one adds two keys a and b such that
 * (!a.equals(b) && a.compareTo(b) == 0) to a sorted
 * set that does not use an explicit comparator, the second add
 * operation returns false (and the size of the sorted set does not increase)
 * because a and b are equivalent from the sorted set's perspective.<p>
 *
 * Virtually all Java core classes that implement Comparable
 * have natural orderings that are consistent with equals.  One
 * exception is {@link BigDecimal}, whose {@link
 * BigDecimal::compareTo} natural ordering equates BigDecimal
 * objects with equal numerical values and different
 * representations (such as 4.0 and 4.00). For {@link
 * BigDecimal::equals()} to return true,
 * the representation and numerical value of the two BigDecimal
 * objects must be the same.<p>
 *
 * <p>This interface is a member of the Java Collections Framework.
 *
 * @see Comparator
 */
interface Comparable extends IObject
{

    /**
     * Compares this object with the specified object for order.  Returns a
     * negative integer, zero, or a positive integer as this object is less
     * than, equal to, or greater than the specified object.
     *
     * @implNote
     * The implementor must ensure: <pre>
     *     $x->compareTo($y) == -TInteger::signum($y.compareTo($x)) forall $x & $y.
     * </pre>
     * <p> This implies that $x->compareTo($y) must throw an exception if and only if
     * $y->compareTo($x) throws an exception. </p> <br>
     *
     * The implementor must also ensure that the relation is transitive: <pre>
     *     $x->compareTo($y) > 0 && $y->compareTo($z) > 0) implies:
     *     $x->compareTo($z) > 0.
     * </pre>
     *
     * Finally, the implementor must ensure that: <pre>
     *     $x.compareTo($y)==0 implies:
     *     signum($x.compareTo($z)) == signum($y.compareTo($z)), for all $z.
     * </pre>
     *
     * It might be useful to use the spaceship operator to implement this method
     *
     * @apiNote
     * It is strongly recommended, but <i>not</i> strictly required that
     * ($x.compareTo($y)==0) == ($x.equals($y)).  Generally speaking, any
     * class that implements the Comparable interface and violates
     * this condition should clearly indicate this fact.  The recommended
     * language is "Note: this class has a natural ordering that is
     * inconsistent with equals."
     *
     * @param   IObject $o the object to be compared.
     * @return  int a negative integer, zero, or a positive integer as this object
     *          is less than, equal to, or greater than the specified object.
     *
     * @throws IllegalArgumentException if the specified object's type prevents it
     *         from being compared to this object.
     * @see    TInteger::signum()
     */
    public function compareTo(IObject $o): int;
}