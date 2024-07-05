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
 * Copyright (c) 1997, 2018, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */

namespace jhp\util\collection;

use ArrayAccess;
use Iterator;
use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\exception\IllegalStateException;
use jhp\lang\exception\UnsupportedOperationException;
use jhp\lang\IIterable;
use jhp\lang\IObject;
use jhp\lang\TClass;
use jhp\util\function\Consumer;
use jhp\util\function\Predicate;
use jhp\util\stream\Stream;

/**
 * The root interface in the <i>collection hierarchy</i>.  A collection
 * represents a group of objects, known as its <i>elements</i>.  Some
 * collections allow duplicate elements and others do not.  Some are ordered
 * and others unordered.  The JDK does not provide any <i>direct</i>
 * implementations of this interface: it provides implementations of more
 * specific subinterfaces like ISet and List.  This interface
 * is typically used to pass collections around and manipulate them where
 * maximum generality is desired.
 *
 * <p><i>Bags</i> or <i>multisets</i> (unordered collections that may contain
 * duplicate elements) should implement this interface directly.
 *
 * <p>All general-purpose ICollection implementation classes (which
 * typically implement ICollection indirectly through one of its
 * subinterfaces) should provide two "standard" constructors: a void (no
 * arguments) constructor, which creates an empty collection, and a
 * constructor with a single argument of type ICollection, which
 * creates a new collection with the same elements as its argument.  In
 * effect, the latter constructor allows the user to copy any collection,
 * producing an equivalent collection of the desired implementation type.
 * There is no way to enforce this convention (as interfaces cannot contain
 * constructors) but all of the general-purpose ICollection
 * implementations in the Java platform libraries comply.
 *
 * <p>Certain methods are specified to be
 * <i>optional</i>. If a collection implementation doesn't implement a
 * particular operation, it should define the corresponding method to throw
 * UnsupportedOperationException. Such methods are marked "optional
 * operation" in method specifications of the collections interfaces.
 *
 * <p><a id="optional-restrictions"></a>Some collection implementations
 * have restrictions on the elements that they may contain.
 * For example, some implementations prohibit null elements,
 * and some have restrictions on the types of their elements.  Attempting to
 * add an ineligible element throws an unchecked exception, typically
 * NullPointerException or ClassCastException.  Attempting
 * to query the presence of an ineligible element may throw an exception,
 * or it may simply return false; some implementations will exhibit the former
 * behavior and some will exhibit the latter.  More generally, attempting an
 * operation on an ineligible element whose completion would not result in
 * the insertion of an ineligible element into the collection may throw an
 * exception or it may succeed, at the option of the implementation.
 * Such exceptions are marked as "optional" in the specification for this
 * interface.
 *
 * <p>It is up to each collection to determine its own synchronization
 * policy.  In the absence of a stronger guarantee by the
 * implementation, undefined behavior may result from the invocation
 * of any method on a collection that is being mutated by another
 * thread; this includes direct invocations, passing the collection to
 * a method that might perform invocations, and using an existing
 * iterator to examine the collection.
 *
 * <p>Many methods in Collections Framework interfaces are defined in
 * terms of the {@link Object#equals(Object) equals} method.  For example,
 * the specification for the {@link #contains(Object) contains(Object o)}
 * method says: "returns true if and only if this collection
 * contains at least one element e such that
 * (o==null ? e==null : o.equals(e))."  This specification should
 * <i>not</i> be construed to imply that invoking ICollection.contains
 * with a non-null argument o will cause o.equals(e) to be
 * invoked for any element e.  Implementations are free to implement
 * optimizations whereby the equals invocation is avoided, for
 * example, by first comparing the hash codes of the two elements.  (The
 * {@link Object#hashCode()} specification guarantees that two objects with
 * unequal hash codes cannot be equal.)  More generally, implementations of
 * the various Collections Framework interfaces are free to take advantage of
 * the specified behavior of underlying {@link Object} methods wherever the
 * implementor deems it appropriate.
 *
 * <p>Some collection operations which perform recursive traversal of the
 * collection may fail with an exception for self-referential instances where
 * the collection directly or indirectly contains itself. This includes the
 * clone(), equals(), hashCode() and toString()
 * methods. Implementations may optionally handle the self-referential scenario,
 * however most current implementations do not do so.
 *
 * <h2><a id="view">View Collections</a></h2>
 *
 * <p>Most collections manage storage for elements they contain. By contrast, <i>view
 * collections</i> themselves do not store elements, but instead they rely on a
 * backing collection to store the actual elements. Operations that are not handled
 * by the view collection itself are delegated to the backing collection. Examples of
 * view collections include the wrapper collections returned by methods such as
 * {@link Collections#checkedCollection Collections.checkedCollection},
 * {@link Collections#synchronizedCollection Collections.synchronizedCollection}, and
 * {@link Collections#unmodifiableCollection Collections.unmodifiableCollection}.
 * Other examples of view collections include collections that provide a
 * different representation of the same elements, for example, as
 * provided by {@link List#subList List.subList},
 * {@link NavigableSet#subSet NavigableSet.subSet}, or
 * {@link IMap#entrySet Map.entrySet}.
 * Any changes made to the backing collection are visible in the view collection.
 * Correspondingly, any changes made to the view collection &mdash; if changes
 * are permitted &mdash; are written through to the backing collection.
 * Although they technically aren't collections, instances of
 * {@link Iterator} and {@link ListIterator} can also allow modifications
 * to be written through to the backing collection, and in some cases,
 * modifications to the backing collection will be visible to the Iterator
 * during iteration.
 *
 * <h2><a id="unmodifiable">Unmodifiable Collections</a></h2>
 *
 * <p>Certain methods of this interface are considered "destructive" and are called
 * "mutator" methods in that they modify the group of objects contained within
 * the collection on which they operate. They can be specified to throw
 * UnsupportedOperationException if this collection implementation
 * does not support the operation. Such methods should (but are not required
 * to) throw an UnsupportedOperationException if the invocation would
 * have no effect on the collection. For example, consider a collection that
 * does not support the {@link #add add} operation. What will happen if the
 * {@link #addAll addAll} method is invoked on this collection, with an empty
 * collection as the argument? The addition of zero elements has no effect,
 * so it is permissible for this collection simply to do nothing and not to throw
 * an exception. However, it is recommended that such cases throw an exception
 * unconditionally, as throwing only in certain cases can lead to
 * programming errors.
 *
 * <p>An <i>unmodifiable collection</i> is a collection, all of whose
 * mutator methods (as defined above) are specified to throw
 * UnsupportedOperationException. Such a collection thus cannot be
 * modified by calling any methods on it. For a collection to be properly
 * unmodifiable, any view collections derived from it must also be unmodifiable.
 * For example, if a List is unmodifiable, the List returned by
 * {@link List#subList List.subList} is also unmodifiable.
 *
 * <p>An unmodifiable collection is not necessarily immutable. If the
 * contained elements are mutable, the entire collection is clearly
 * mutable, even though it might be unmodifiable. For example, consider
 * two unmodifiable lists containing mutable elements. The result of calling
 * list1.equals(list2) might differ from one call to the next if
 * the elements had been mutated, even though both lists are unmodifiable.
 * However, if an unmodifiable collection contains all immutable elements,
 * it can be considered effectively immutable.
 *
 * <h2><a id="unmodview">Unmodifiable View Collections</a></h2>
 *
 * <p>An <i>unmodifiable view collection</i> is a collection that is unmodifiable
 * and that is also a view onto a backing collection. Its mutator methods throw
 * UnsupportedOperationException, as described above, while
 * reading and querying methods are delegated to the backing collection.
 * The effect is to provide read-only access to the backing collection.
 * This is useful for a component to provide users with read access to
 * an internal collection, while preventing them from modifying such
 * collections unexpectedly. Examples of unmodifiable view collections
 * are those returned by the
 * {@link Collections#unmodifiableCollection Collections.unmodifiableCollection},
 * {@link Collections#unmodifiableList Collections.unmodifiableList}, and
 * related methods.
 *
 * <p>Note that changes to the backing collection might still be possible,
 * and if they occur, they are visible through the unmodifiable view. Thus,
 * an unmodifiable view collection is not necessarily immutable. However,
 * if the backing collection of an unmodifiable view is effectively immutable,
 * or if the only reference to the backing collection is through an
 * unmodifiable view, the view can be considered effectively immutable.
 *
 * <h2><a id="serializable">Serializability of Collections</a></h2>
 *
 * <p>Serializability of collections is optional. As such, none of the collections
 * interfaces are declared to implement the {@link java.io.Serializable} interface.
 * However, serializability is regarded as being generally useful, so most collection
 * implementations are serializable.
 *
 * <p>The collection implementations that are public classes (such as ArrayList
 * or HashMap) are declared to implement the Serializable interface if they
 * are in fact serializable. Some collections implementations are not public classes,
 * such as the <a href="#unmodifiable">unmodifiable collections.</a> In such cases, the
 * serializability of such collections is described in the specification of the method
 * that creates them, or in some other suitable place. In cases where the serializability
 * of a collection is not specified, there is no guarantee about the serializability of such
 * collections. In particular, many <a href="#view">view collections</a> are not serializable.
 *
 * <p>A collection implementation that implements the Serializable interface cannot
 * be guaranteed to be serializable. The reason is that in general, collections
 * contain elements of other types, and it is not possible to determine statically
 * whether instances of some element type are actually serializable. For example, consider
 * a serializable ICollection<E>, where E does not implement the
 * Serializable interface. The collection may be serializable, if it contains only
 * elements of some serializable subtype of E, or if it is empty. Collections are
 * thus said to be <i>conditionally serializable,</i> as the serializability of the collection
 * as a whole depends on whether the collection itself is serializable and on whether all
 * contained elements are also serializable.
 *
 * <p>An additional case occurs with instances of {@link SortedSet} and {@link SortedMap}.
 * These collections can be created with a {@link Comparator} that imposes an ordering on
 * the set elements or map keys. Such a collection is serializable only if the provided
 * Comparator is also serializable.
 *
 * <p>This interface is a member of the
 * <a href="{@docRoot}/java.base/java/util/package-summary.html#CollectionsFramework">
 * Java Collections Framework</a>.
 *
 * @implSpec
 * The default method implementations (inherited or otherwise) do not apply any
 * synchronization protocol.  If a ICollection implementation has a
 * specific synchronization protocol, then it must override default
 * implementations to apply that protocol.
 *
 * @param <E> the type of elements in this collection
 *
 * @author  Josh Bloch
 * @author  Neal Gafter
 * @see     ISet
 * @see     List
 * @see     IMap
 * @see     SortedSet
 * @see     SortedMap
 * @see     HashSet
 * @see     TreeSet
 * @see     ArrayList
 * @see     LinkedList
 * @see     Vector
 * @see     Collections
 * @see     Arrays
 * @see     AbstractCollection
 * @since 1.2
 */
interface ICollection extends IIterable, ArrayAccess, IObject
{
    // Query Operations

    /**
     * Returns the number of elements in this collection.
     * The maximum size of an array is the size of an 32-bit integer in PHP, even if the 64-bit php is used
     *
     * @see https://stackoverflow.com/a/73885850
     *
     * @return int the number of elements in this collection
     */
    public function size(): int;

    /**
     * Returns true if this collection contains no elements.
     *
     * @return true if this collection contains no elements
     */
    public function isEmpty(): bool;

    /**
     * Returns true if this collection contains the specified element.
     * More formally, returns true if and only if this collection
     * contains at least one element e such that
     * Objects.equals(o, e).
     *
     * @param IObject $o element whose presence in this collection is to be tested
     *
     * @return bool true if this collection contains the specified element
     *
     * @throws IllegalArgumentException if the element is not of the same type as the list
     */
    public function contains(IObject $o): bool;

    /**
     * Returns an array containing all the elements in this collection;
     * the runtime type of the returned array is that of the specified array.
     *
     * <p>If this collection fits in the specified array with room to spare
     * (i.e., the array has more elements than this collection), the element
     * in the array immediately following the end of the collection is set to
     * null.  (This is useful in determining the length of this
     * collection <i>only</i> if the caller knows that this collection does
     * not contain any null elements.)
     *
     * <p>If this collection makes any guarantees as to what order its elements
     * are returned by its iterator, this method must return the elements in
     * the same order.
     *
     * @note
     * In JHP collections should never have null elements
     *
     * @apiNote
     * This method acts as a bridge between array-based and collection-based APIs.
     * It allows an existing array to be reused under certain circumstances.
     *
     * <p>Suppose x is a collection known to contain only strings.
     * The following code can be used to dump the collection into a previously
     * allocated String array:
     *
     * <pre>
     *     String[] y = new String[SIZE];
     *     ...
     *     y = x.toArray(y);</pre>
     *
     * <p>The return value is reassigned to the variable y, because a
     * new array will be allocated and returned if the collection x has
     * too many elements to fit into the existing array y.
     *
     * <p>Note that toArray(new Object[0]) is identical in function to
     * toArray().
     *
     * @param array &$a the array into which the elements of this collection are to be
     *        stored, if it is big enough; otherwise, a new array of the same
     *        runtime type is allocated for this purpose.
     *
     * @return array an array containing all the elements in this collection
     */
    public function toArray(array &$a = []): array;

    // Modification Operations

    /**
     * Ensures that this collection contains the specified element (optional
     * operation).  Returns true if this collection changed as a
     * result of the call.  (Returns false if this collection does
     * not permit duplicates and already contains the specified element.)<p>
     *
     * Collections that support this operation may place limitations on what
     * elements may be added to this collection.  In particular, some
     * collections will refuse to add null elements, and others will
     * impose restrictions on the type of elements that may be added.
     * ICollection classes should clearly specify in their documentation any
     * restrictions on what elements may be added.<p>
     *
     * If a collection refuses to add a particular element for any reason
     * other than that it already contains the element, it <i>must</i> throw
     * an exception (rather than returning false).  This preserves
     * the invariant that a collection always contains the specified element
     * after this call returns.
     *
     * @ImplNote
     * In the JHP library we do not allow null to be added to any collection.
     *
     * @param IObject $a element whose presence in this collection is to be ensured
     *
     * @return bool true if this collection changed as a result of the call
     *
     * @throws UnsupportedOperationException if the add operation
     *         is not supported by this collection
     * @throws IllegalArgumentException if some property of the element
     *         prevents it from being added to this collection
     * @throws IllegalStateException if the element cannot be added at this
     *         time due to insertion restrictions
     */
    public function add(IObject $a): bool;


    // Bulk Operations

    /**
     * Returns true if this collection contains all the elements in the specified collection.
     *
     * @param ICollection $c collection to be checked for containment in this collection
     *
     * @return true if this collection contains all the elements in the specified collection
     *
     * @throws IllegalArgumentException if the element is not of the same type as the list
     * @see    ICollection::contains()
     */
    public function containsAll(ICollection $c): bool;

    /**
     * Adds all the elements in the specified collection to this collection
     * (optional operation).  The behavior of this operation is undefined if
     * the specified collection is modified while the operation is in progress.
     * (This implies that the behavior of this call is undefined if the
     * specified collection is this collection, and this collection is
     * nonempty.)
     *
     * @param c collection containing elements to be added to this collection
     *
     * @return true if this collection changed as a result of the call
     * @throws UnsupportedOperationException if the addAll operation
     *         is not supported by this collection
     * @throws ClassCastException if the class of an element of the specified
     *         collection prevents it from being added to this collection
     * @throws NullPointerException if the specified collection contains a
     *         null element and this collection does not permit null elements,
     *         or if the specified collection is null
     * @throws IllegalArgumentException if some property of an element of the
     *         specified collection prevents it from being added to this
     *         collection
     * @throws IllegalStateException if not all the elements can be added at
     *         this time due to insertion restrictions
     * @see #add(Object)
     */
    public function addAll(ICollection $a): bool;

    /**
     * Removes all of this collection's elements that are also contained in the
     * specified collection.  After this call returns,
     * this collection will contain no elements in common with the specified
     * collection.
     *
     * @param ICollection $c collection containing elements to be removed from this collection
     *
     * @return true if this collection changed as a result of the call
     *
     * @throws UnsupportedOperationException if the removeAll method
     *         is not supported by this collection
     * @throws IllegalArgumentException if the types of one or more elements
     *         in this collection are incompatible with the specified
     *         collection
     * @see ICollection::remove(Object)
     * @see ICollection::contains(Object)
     */
    public function removeAll(ICollection $c): bool;

    /**
     * Removes all the elements of this collection that satisfy the given
     * predicate.  Errors or runtime exceptions thrown during iteration or by
     * the predicate are relayed to the caller.
     *
     * @implSpec
     * The default implementation traverses all elements of the collection using
     * its {@link #iterator}.  Each matching element is removed using
     * {@link Iterator#remove()}.  If the collection's iterator does not
     * support removal then an UnsupportedOperationException will be
     * thrown on the first matching element.
     *
     * @param Predicate $filter a predicate which returns true for elements to be
     *        removed
     *
     * @return true if any elements were removed
     * @throws UnsupportedOperationException if elements cannot be removed
     *         from this collection.  Implementations may throw this exception if a
     *         matching element cannot be removed or if, in general, removal is not
     *         supported.
     */
    public function removeIf(Predicate $filter): bool;

    /**
     * Retains only the elements in this collection that are contained in the
     * specified collection (optional operation).  In other words, removes from
     * this collection all of its elements that are not contained in the
     * specified collection.
     *
     * @param ICollection $c collection containing elements to be retained in this collection
     *
     * @return true if this collection changed as a result of the call
     * @throws UnsupportedOperationException if the retainAll operation
     *         is not supported by this collection
     * @throws IllegalArgumentException if the types of one or more elements
     *         in this collection are incompatible with the specified
     *         collection
     * @see ICollection::remove(Object)
     * @see ICollection::contains(Object)
     */
    public function retainAll(ICollection $c): bool;

    /**
     * Removes all the elements from this collection (optional operation).
     * The collection will be empty after this method returns.
     *
     * @throws UnsupportedOperationException if the clear operation
     *         is not supported by this collection
     */
    public function clear(): void;

    /**
     * Returns a sequential Stream with this collection as its source.
     *
     * <p>This method should be overridden when the {@link #spliterator()}
     * method cannot return a spliterator that is IMMUTABLE,
     * CONCURRENT, or <em>late-binding</em>. (See {@link #spliterator()}
     * for details.)
     *
     * @implSpec
     * The default implementation creates a sequential Stream from the
     * collection's Spliterator.
     *
     * @return a sequential Stream over the elements in this collection
     * @since 1.8
     */
    public function stream(): Stream;

    /**
     * Returns a possibly parallel Stream with this collection as its
     * source.  It is allowable for this method to return a sequential stream.
     *
     * <p>This method should be overridden when the {@link #spliterator()}
     * method cannot return a spliterator that is IMMUTABLE,
     * CONCURRENT, or <em>late-binding</em>. (See {@link #spliterator()}
     * for details.)
     *
     * @implSpec
     * The default implementation creates a parallel Stream from the
     * collection's Spliterator.
     *
     * @return a possibly parallel Stream over the elements in this
     * collection
     * @since 1.8
     */
    public function parallelStream(): Stream;

    /**
     * Performs the given action for each element of the Iterable
     * until all elements have been processed or the action throws an
     * exception.  Actions are performed in the order of iteration, if that
     * order is specified.  Exceptions thrown by the action are relayed to the
     * caller.
     * <p>
     * The behavior of this method is unspecified if the action performs
     * side effects that modify the underlying source of elements, unless an
     * overriding class has specified a concurrent modification policy.
     *
     * @implSpec
     * <p>The default implementation behaves as if:
     * <pre>{@code
     *     for (IObject t : this)
     *         action.accept(t);
     * }</pre>
     *
     * @param Consumer $action The action to be performed for each element
     */
    public function forEach(Consumer $action);

    /**
     * Gets the type of the elements contained in this collection
     *
     * @return TClass the type of the elements in this collection
     */
    public function getType(): TClass;
}
