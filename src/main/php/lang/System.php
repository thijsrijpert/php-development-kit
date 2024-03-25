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

class System extends TObject
{

    private function __construct() {}

    /**
     *  Returns a hash code value for the object. This method is
     *  supported for the benefit of hash tables such as those provided by
     *  {@link THashMap}.
     *  <p>
     *  The general contract of hashCode is:
     *  <ul>
     *  <li>Whenever it is invoked on the same object more than once during
     *      an execution of a Java application, the hashCode method
     *      must consistently return the same integer, provided no information
     *      used in equals comparisons on the object is modified.
     *      This integer need not remain consistent from one execution of an
     *      application to another execution of the same application.
     *  <li>If two objects are equal according to the {@link TObject::equals()}
     *      method, then calling the hashCode method on each of the two objects
     *      must produce the same integer result.
     *  <li>It is <em>not</em> required that if two objects are unequal
     *      according to the {@link TObject::equals(Object)} method, then
     *      calling the hashCode method on each of the two objects
     *      must produce distinct integer results.  However, the programmer
     *      should be aware that producing distinct integer results for
     *      unequal objects may improve the performance of hash tables.
     *  </ul>
     *
     * @param IObject $object The object to retrieve the hashcode for
     *
     * @return int The hashcode of the object
     */
    public static function identityHashCode(IObject $object): int {
        return spl_object_id($object);
    }
}