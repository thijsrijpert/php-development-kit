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

use jhp\lang\exception\CloneNotSupportedException;

interface IObject
{
    /**
     * Returns the runtime class of this TObject.
     *
     * @return TClass The class object that represents the runtime class of this object.
     */
    public function getClass(): TClass;

    /**
     * Returns a hash code value for the object. This method is
     * supported for the benefit of hash tables such as those provided by
     * {@link HashMap}.
     * <p>
     * The general contract of hashCode is:
     * <ul>
     * <li>Whenever it is invoked on the same object more than once during
     *     an execution of a Java application, the hashCode method
     *     must consistently return the same integer, provided no information
     *     used in equals comparisons on the object is modified.
     *     This integer need not remain consistent from one execution of an
     *     application to another execution of the same application.
     * <li>If two objects are equal according to the {@link TObject::equals()}
     *     method, then calling the hashCode method on each of the two objects
     *     must produce the same integer result.
     * <li>It is <em>not</em> required that if two objects are unequal
     *     according to the {@link TObject::equals(Object)} method, then
     *     calling the hashCode method on each of the two objects
     *     must produce distinct integer results.  However, the programmer
     *     should be aware that producing distinct integer results for
     *     unequal objects may improve the performance of hash tables.
     * </ul>
     *
     * @implSpec
     * As far as is reasonably practical, the hashCode method defined
     * by class Object returns distinct integers for distinct objects.
     *
     * @return  int a hash code value for this object.
     * @see     TObject::equals()
     * @see     System::identityHashCode()
     */
    public function hashCode(): int;


    /**
     * Indicates whether some other object is "equal to" this one.
     * <p>
     * The equals method implements an equivalence relation
     * on non-null object references:
     * <ul>
     * <li>It is <i>reflexive</i>: for any non-null reference value
     *     x, x.equals(x) should return
     *     true.
     * <li>It is <i>symmetric</i>: for any non-null reference values
     *     x and y, x.equals(y)
     *     should return true if and only if
     *     y.equals(x) returns true.
     * <li>It is <i>transitive</i>: for any non-null reference values
     *     x, y, and z, if
     *     x.equals(y) returns true and
     *     y.equals(z) returns true, then
     *     x.equals(z) should return true.
     * <li>It is <i>consistent</i>: for any non-null reference values
     *     x and y, multiple invocations of
     *     x.equals(y) consistently return true
     *     or consistently return false, provided no
     *     information used in equals comparisons on the
     *     objects is modified.
     * <li>For any non-null reference value x,
     *     x.equals(null) should return false.
     * </ul>
     *
     * <p>
     * An equivalence relation partitions the elements it operates on
     * into <i>equivalence classes</i>; all the members of an
     * equivalence class are equal to each other. Members of an
     * equivalence class are substitutable for each other, at least
     * for some purposes.
     *
     * The equals method for class Object implements
     * the most discriminating possible equivalence relation on objects;
     * that is, for any non-null reference values x and
     * y, this method returns true if and only
     * if x and y refer to the same object
     * (x === y has the value true).
     *
     * @param   ?TObject $obj the reference object with which to compare.
     *
     * @return  bool true if this object is the same as the obj argument; false otherwise.
     * @api
     * It is generally necessary to override the {@link TObject::hashCode}
     * method whenever this method is overridden, to maintain the
     * general contract for the hashCode method, which states
     * that equal objects must have equal hash codes.
     *
     * @see     TObject::hashCode()
     * @see     HashMap
     */
    public function equals(?TObject $obj = null): bool;

    /**
     * Creates and returns a copy of this object.  The precise meaning
     * of "copy" may depend on the class of the object. The general
     * intent is that, for any object x, the expression:
     * <blockquote>
     * <pre>
     * x.clone() !== x</pre></blockquote>
     * will be true, and that the expression:
     * <blockquote>
     * <pre>
     * x.clone().getClass() == x.getClass()</pre></blockquote>
     * will be true, but these are not absolute requirements.
     * While it is typically the case that:
     * <blockquote>
     * <pre>
     * x.clone().equals(x)</pre></blockquote>
     * will be true, this is not an absolute requirement.
     * <p>
     * By convention, the returned object should be obtained by calling
     * super.clone.  If a class and all of its superclasses (except
     * Object) obey this convention, it will be the case that
     * x.clone().getClass() == x.getClass().
     * <p>
     * By convention, the object returned by this method should be independent
     * of this object (which is being cloned).  To achieve this independence,
     * it may be necessary to modify one or more fields of the object returned
     * by super.clone() before returning it.  Typically, this means
     * copying any mutable objects that comprise the internal "deep structure"
     * of the object being cloned and replacing the references to these
     * objects with references to the copies.  If a class contains only
     * primitive fields or references to immutable objects, then it is usually
     * the case that no fields in the object returned by super.clone()
     * need to be modified.
     *
     * The method clone for class object performs a
     * specific cloning operation. First, if the class of this object does
     * not implement the interface Cloneable, then a
     * CloneNotSupportedException is thrown.
     * Otherwise, this method creates a new instance of the class of this
     * object and initializes all its fields with exactly the contents of
     * the corresponding fields of this object, as if by assignment; the
     * contents of the fields are not themselves cloned. Thus, this method
     * performs a "shallow copy" of this object, not a "deep copy" operation.
     * <p>
     * The class TObject does not itself implement the interface
     * Cloneable, so calling the clone method on an object
     * whose class is TObject will result in throwing an
     * exception at runtime.
     *
     * @return     TObject a clone of this instance.
     * @throws     CloneNotSupportedException  if the object's class does not
     *               support the Cloneable interface. Subclasses
     *               that override the clone method can also
     *               throw this exception to indicate that an instance cannot
     *               be cloned.
     * @see java.lang.Cloneable
     */
    public function clone(): TObject;


    /**
     * Returns a string representation of the object.
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
     * object is an instance, the at-sign character `@', and
     * the unsigned hexadecimal representation of the hash code of the
     * object.
     *
     * @return  string a string representation of the object.
     * @see TClass::getName()
     * @see TObject::getClass()
     * @see TInteger::toHexString()
     * @see TObject::hashCode()
     */
    public function toString(): string;

    /**
     * Causes the current thread to be paused for the supplied timeout
     *
     * @param int $timeout Timeout in milliseconds
     * @param int $nanos Timeout in nanoseconds (Not really used)
     */
    public function wait(int $timeout = 0, int $nanos = 0): void;
}