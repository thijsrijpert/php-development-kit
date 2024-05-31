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
 * Copyright (c) 1997, 2020, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 *
 */
namespace jhp\util\collection;

use ArrayAccess;
use jhp\lang\IIterable;
use jhp\lang\IObject;
use jhp\lang\TClass;
use jhp\util\function\BiConsumer;
use jhp\util\function\BiFunction;
use jhp\util\function\Consumer;
use jhp\util\function\GFunction;

/**
 * An object that maps keys to values.  A map cannot contain duplicate keys;
 * each key can map to at most one value.
 *
 * <p>This interface takes the place of the Dictionary} class, which
 * was a totally abstract class rather than an interface.
 *
 * <p>The Map} interface provides three <i>collection views</i>, which
 * allow a map's contents to be viewed as a set of keys, collection of values,
 * or set of key-value mappings.  The <i>order</i> of a map is defined as
 * the order in which the iterators on the map's collection views return their
 * elements.  Some map implementations, like the TreeMap} class, make
 * specific guarantees as to their order; others, like the HashMap}
 * class, do not.
 *
 * <p>Note: great care must be exercised if mutable objects are used as map
 * keys.  The behavior of a map is not specified if the value of an object is
 * changed in a manner that affects equals} comparisons while the
 * object is a key in the map.  A special case of this prohibition is that it
 * is not permissible for a map to contain itself as a key.  While it is
 * permissible for a map to contain itself as a value, extreme caution is
 * advised: the equals} and hashCode} methods are no longer
 * well defined on such a map.
 *
 * <p>All general-purpose map implementation classes should provide two
 * "standard" constructors: a void (no arguments) constructor which creates an
 * empty map, and a constructor with a single argument of type Map},
 * which creates a new map with the same key-value mappings as its argument.
 * In effect, the latter constructor allows the user to copy any map,
 * producing an equivalent map of the desired class.  There is no way to
 * enforce this recommendation (as interfaces cannot contain constructors) but
 * all of the general-purpose map implementations in the JDK comply.
 *
 * <p>The "destructive" methods contained in this interface, that is, the
 * methods that modify the map on which they operate, are specified to throw
 * UnsupportedOperationException} if this map does not support the
 * operation.  If this is the case, these methods may, but are not required
 * to, throw an UnsupportedOperationException} if the invocation would
 * have no effect on the map.  For example, invoking the {@link #putAll(Map)}
 * method on an unmodifiable map may, but is not required to, throw the
 * exception if the map whose mappings are to be "superimposed" is empty.
 *
 * <p>Some map implementations have restrictions on the keys and values they
 * may contain.  For example, some implementations prohibit null keys and
 * values, and some have restrictions on the types of their keys.  Attempting
 * to insert an ineligible key or value throws an unchecked exception,
 * typically NullPointerException} or ClassCastException}.
 * Attempting to query the presence of an ineligible key or value may throw an
 * exception, or it may simply return false; some implementations will exhibit
 * the former behavior and some will exhibit the latter.  More generally,
 * attempting an operation on an ineligible key or value whose completion
 * would not result in the insertion of an ineligible element into the map may
 * throw an exception or it may succeed, at the option of the implementation.
 * Such exceptions are marked as "optional" in the specification for this
 * interface.
 *
 * <p>Many methods in Collections Framework interfaces are defined
 * in terms of the {@link Object#equals(Object) equals} method.  For
 * example, the specification for the {@link #containsKey(Object)
 * containsKey(Object key)} method says: "returns true if and
 * only if this map contains a mapping for a key k such that
 * (key==null ? k==null : key.equals(k))}." This specification should
 * <i>not</i> be construed to imply that invoking Map.containsKey}
 * with a non-null argument key} will cause key.equals(k)} to
 * be invoked for any key k.  Implementations are free to
 * implement optimizations whereby the equals} invocation is avoided,
 * for example, by first comparing the hash codes of the two keys.  (The
 * {@link Object#hashCode()} specification guarantees that two objects with
 * unequal hash codes cannot be equal.)  More generally, implementations of
 * the various Collections Framework interfaces are free to take advantage of
 * the specified behavior of underlying {@link Object} methods wherever the
 * implementor deems it appropriate.
 *
 * <p>Some map operations which perform recursive traversal of the map may fail
 * with an exception for self-referential instances where the map directly or
 * indirectly contains itself. This includes the clone()},
 * equals()}, hashCode()} and toString()} methods.
 * Implementations may optionally handle the self-referential scenario, however
 * most current implementations do not do so.
 *
 * <h2><a id="unmodifiable">Unmodifiable Maps</a></h2>
 * <p>The {@link IMap#of() Map.of},
 * {@link IMap#ofEntries(Map.Entry...) Map.ofEntries}, and
 * {@link IMap#copyOf Map.copyOf}
 * static factory methods provide a convenient way to create unmodifiable maps.
 * The Map}
 * instances created by these methods have the following characteristics:
 *
 * <ul>
 * <li>They are <a href="Collection.html#unmodifiable"><i>unmodifiable</i></a>. Keys and values
 * cannot be added, removed, or updated. Calling any mutator method on the Map
 * will always cause UnsupportedOperationException} to be thrown.
 * However, if the contained keys or values are themselves mutable, this may cause the
 * Map to behave inconsistently or its contents to appear to change.
 * <li>They disallow null} keys and values. Attempts to create them with
 * null} keys or values result in NullPointerException}.
 * <li>They are serializable if all keys and values are serializable.
 * <li>They reject duplicate keys at creation time. Duplicate keys
 * passed to a static factory method result in IllegalArgumentException}.
 * <li>The iteration order of mappings is unspecified and is subject to change.
 * <li>They are <a href="../lang/doc-files/ValueBased.html">value-based</a>.
 * Programmers should treat instances that are {@linkplain #equals(Object) equal}
 * as interchangeable and should not use them for synchronization, or
 * unpredictable behavior may occur. For example, in a future release,
 * synchronization may fail. Callers should make no assumptions
 * about the identity of the returned instances. Factories are free to
 * create new instances or reuse existing ones.
 * <li>They are serialized as specified on the
 * <a href="{@docRoot}/serialized-form.html#java.util.CollSer">Serialized Form</a>
 * page.
 * </ul>
 *
 * <p>This interface is a member of the
 * <a href="{@docRoot}/java.base/java/util/package-summary.html#CollectionsFramework">
 * Java Collections Framework</a>.
 *
 *
 * @author  Josh Bloch
 * @see THashMap
 * @see TreeMap
 * @see Hashtable
 * @see SortedMap
 * @see Collection
 * @see ISet
 */
interface IMap extends IIterable, ArrayAccess, IObject {

    public function getKeyType(): TClass;

    public function getValueType(): TClass;

    // Query Operations

    /**
     * Returns the number of key-value mappings in this map.  If the
     * map contains more than Integer.MAX_VALUE} elements, returns
     * Integer.MAX_VALUE}.
     *
     * @return int the number of key-value mappings in this map
     */
    public function size(): int;

    /**
     * Returns true if this map contains no key-value mappings.
     *
     * @return bool true if this map contains no key-value mappings
     */
    public function isEmpty(): bool;

    /**
     * Returns true if this map contains a mapping for the specified
     * key.  More formally, returns true if and only if
     * this map contains a mapping for a key k such that
     * Objects.equals(key, k)}.  (There can be
     * at most one such mapping.)
     *
     * @param IObject $key key whose presence in this map is to be tested
     *
     * @return bool true if this map contains a mapping for the specified key
     */
    public function containsKey(IObject $key): bool;

    /**
     * Returns true if this map maps one or more keys to the
     * specified value. More formally, returns true if and only if
     * this map contains at least one mapping to a value v such that
     * Objects.equals(value, v).  This operation
     * will probably require time linear in the map size for most
     * implementations of the Map interface.
     *
     * @param IObject $value value whose presence in this map is to be tested
     *
     * @return bool true if this map maps one or more keys to the
     *         specified value
     */
    public function containsValue(IObject $value): bool;

    /**
     * Returns the value to which the specified key is mapped,
     * or null if this map contains no mapping for the key.
     *
     * <p>More formally, if this map contains a mapping from a key
     * k to a value v such that
     * Objects.equals(key, k),
     * then this method returns v; otherwise
     * it returns null.  (There can be at most one such mapping.)
     *
     * <p>If this map permits null values, then a return value of
     * null does not <i>necessarily</i> indicate that the map
     * contains no mapping for the key; it's also possible that the map
     * explicitly maps the key to null.  The {@link #containsKey
     * containsKey} operation may be used to distinguish these two cases.
     *
     * @param IObject $key the key whose associated value is to be returned
     *
     * @return ?IObject the value to which the specified key is mapped, or
     *         null if this map contains no mapping for the key
     */
    public function get(IObject $key): ?IObject;

    // Modification Operations

    /**
     * Associates the specified value with the specified key in this map
     * (optional operation).  If the map previously contained a mapping for
     * the key, the old value is replaced by the specified value.  (A map
     * m is said to contain a mapping for a key k if and only
     * if {@link #containsKey(Object) m.containsKey(k)} would return
     * true.)
     *
     * @param IObject $key key with which the specified value is to be associated
     * @param IObject $value value to be associated with the specified key
     *
     * @return ?IObject the previous value associated with key, or
     *         null if there was no mapping for key.
     */
    public function put(IObject $key, IObject $value): ?IObject;


    // Bulk Operations

    /**
     * Copies all the mappings from the specified map to this map
     * (optional operation).  The effect of this call is equivalent to that
     * of calling {@link #put(Object,Object) put(k, v)} on this map once
     * for each mapping from key k to value v in the
     * specified map.  The behavior of this operation is undefined if the
     * specified map is modified while the operation is in progress.
     *
     * @param IMap $m mappings to be stored in this map
     */
    public function putAll(IMap $m): void;

    /**
     * Removes all the mappings from this map (optional operation).
     * The map will be empty after this call returns.
     */
    public function clear(): void;


    // Views

    /**
     * Returns a {@link ISet} view of the keys contained in this map.
     * The set is backed by the map, so changes to the map are
     * reflected in the set, and vice-versa.  If the map is modified
     * while an iteration over the set is in progress (except through
     * the iterator's own remove operation), the results of
     * the iteration are undefined.  The set supports element removal,
     * which removes the corresponding mapping from the map, via the
     * Iterator->remove, ISet->remove,
     * removeAll, retainAll, and clear
     * operations.  It does not support the add or addAll
     * operations.
     *
     * @return ISet a set view of the keys contained in this map
     */
     public function keySet(): ISet;

    /**
     * Returns a {@link Collection} view of the values contained in this map.
     * The collection is backed by the map, so changes to the map are
     * reflected in the collection, and vice-versa.  If the map is
     * modified while an iteration over the collection is in progress
     * (except through the iterator's own remove operation),
     * the results of the iteration are undefined.  The collection
     * supports element removal, which removes the corresponding
     * mapping from the map, via the Iterator->remove,
     * Collection->remove, removeAll,
     * retainAll and clear operations.  It does not
     * support the add or addAll operations.
     *
     * @return ICollection a collection view of the values contained in this map
     */
    public function values(): ICollection;

    /**
     * Returns a {@link ISet} view of the mappings contained in this map.
     * The set is backed by the map, so changes to the map are
     * reflected in the set, and vice-versa.  If the map is modified
     * while an iteration over the set is in progress (except through
     * the iterator's own remove operation, or through the
     * setValue operation on a map entry returned by the
     * iterator) the results of the iteration are undefined.  The set
     * supports element removal, which removes the corresponding
     * mapping from the map, via the Iterator->remove,
     * ISet->remove, removeAll, retainAll and
     * clear operations.  It does not support the
     * add or addAll operations.
     *
     * @return ISet a set view of the mappings contained in this map
     */
    public function entrySet(): ISet;

    // Defaultable methods

    /**
     * Returns the value to which the specified key is mapped, or
     * defaultValue} if this map contains no mapping for the key.
     *
     * @implSpec
     * The default implementation makes no guarantees about synchronization
     * or atomicity properties of this method. Any implementation providing
     * atomicity guarantees must override this method and document its
     * concurrency properties.
     *
     * @param IObject $key the key whose associated value is to be returned
     * @param IObject $defaultValue the default mapping of the key
     *
     * @return IObject the value to which the specified key is mapped, or
     * defaultValue if this map contains no mapping for the key
     */
    public function getOrDefault(IObject $key, IObject $defaultValue): IObject;

    /**
     * Performs the given action for each entry in this map until all entries
     * have been processed or the action throws an exception.   Unless
     * otherwise specified by the implementing class, actions are performed in
     * the order of entry set iteration (if an iteration order is specified.)
     * Exceptions thrown by the action are relayed to the caller.
     *
     * @implSpec
     * The default implementation is equivalent to, for this map}:
     * <pre> {@code
     * for (Map.Entry<K, V> entry : map.entrySet())
     *     action.accept(entry.getKey(), entry.getValue());
     * }</pre>
     *
     * The default implementation makes no guarantees about synchronization
     * or atomicity properties of this method. Any implementation providing
     * atomicity guarantees must override this method and document its
     * concurrency properties.
     *
     * @param BiConsumer $action The action to be performed for each entry
     */
    public function forEach(BiConsumer $action): void;

    /**
     * Replaces each entry's value with the result of invoking the given
     * function on that entry until all entries have been processed or the
     * function throws an exception.  Exceptions thrown by the function are
     * relayed to the caller.
     *
     * @implSpec
     * <p>The default implementation is equivalent to, for this map}:
     * <pre> {@code
     * for (Map.Entry<K, V> entry : map.entrySet())
     *     entry.setValue(function.apply(entry.getKey(), entry.getValue()));
     * }</pre>
     *
     * <p>The default implementation makes no guarantees about synchronization
     * or atomicity properties of this method. Any implementation providing
     * atomicity guarantees must override this method and document its
     * concurrency properties.
     *
     * @param BiFunction $function the function to apply to each entry
     */
    public function replaceAll(BiFunction $function): void;

    /**
     * If the specified key is not already associated with a value (or is mapped
     * to null) associates it with the given value and returns
     * null, else returns the current value.
     *
     * @implSpec
     * The default implementation is equivalent to, for this map}:
     *
     * <pre> {@code
     * IObject v = map.get(key);
     * if (v == null)
     *     v = map.put(key, value);
     *
     * return v;
     * }</pre>
     *
     * <p>The default implementation makes no guarantees about synchronization
     * or atomicity properties of this method. Any implementation providing
     * atomicity guarantees must override this method and document its
     * concurrency properties.
     *
     * @param IObject $key key with which the specified value is to be associated
     * @param IObject $value value to be associated with the specified key
     *
     * @return IObject the previous value associated with the specified key, or
     *         null if there was no mapping for the key.
     *         (A null return can also indicate that the map
     *         previously associated null with the key,
     *         if the implementation supports null values.)
     */
    public function putIfAbsent(IObject $key, IObject $value): IObject;

    /**
     * Removes the entry for the specified key only if it is currently
     * mapped to the specified value.
     *
     * @implSpec
     * The default implementation is equivalent to, for this map}:
     *
     * <pre> {@code
     * if (map.containsKey(key) && Objects.equals(map.get(key), value)) {
     *     map.remove(key);
     *     return true;
     * } else
     *     return false;
     * }</pre>
     *
     * <p>The default implementation makes no guarantees about synchronization
     * or atomicity properties of this method. Any implementation providing
     * atomicity guarantees must override this method and document its
     * concurrency properties.
     *
     * @param IObject $key key with which the specified value is associated
     * @param ?IObject $value value expected to be associated with the specified key (optional)
     *
     * @return bool true if the value was removed
     */
    public function remove(IObject $key, ?IObject $value = null): ?IObject;

    /**
     * Replaces the entry for the specified key only if currently
     * mapped to the specified value.
     *
     * @implSpec
     * The default implementation is equivalent to, for this map}:
     *
     * <pre> {@code
     * if (map.containsKey(key) && Objects.equals(map.get(key), oldValue)) {
     *     map.put(key, newValue);
     *     return true;
     * } else
     *     return false;
     * }</pre>
     *
     * The default implementation does not throw NullPointerException
     * for maps that do not support null values if oldValue is null unless
     * newValue is also null.
     *
     * <p>The default implementation makes no guarantees about synchronization
     * or atomicity properties of this method. Any implementation providing
     * atomicity guarantees must override this method and document its
     * concurrency properties.
     *
     * @param IObject $key key with which the specified value is associated
     * @param IObject $value value expected to be associated with the specified key,
     *                       or the new value if the third parameter is null
     * @param ?IObject $newValue value to be associated with the specified key
     *
     * @return bool true if the value was replaced
     */
    public function replace(IObject $key, IObject $value, ?IObject $newValue = null): ?IObject;

    /**
     * If the specified key is not already associated with a value (or is mapped
     * to null}), attempts to compute its value using the given mapping
     * function and enters it into this map unless null}.
     *
     * <p>If the mapping function returns null}, no mapping is recorded.
     * If the mapping function itself throws an (unchecked) exception, the
     * exception is rethrown, and no mapping is recorded.  The most
     * common usage is to construct a new object serving as an initial
     * mapped value or memoized result, as in:
     *
     * <pre> {@code
     * map.computeIfAbsent(key, k -> new Value(f(k)));
     * }</pre>
     *
     * <p>Or to implement a multi-value map, Map<K,Collection<V>>},
     * supporting multiple values per key:
     *
     * <pre> {@code
     * map.computeIfAbsent(key, k -> new HashSet<V>()).add(v);
     * }</pre>
     *
     * <p>The mapping function should not modify this map during computation.
     *
     * @implSpec
     * The default implementation is equivalent to the following steps for this
     * map}, then returning the current value or null} if now
     * absent:
     *
     * <pre> {@code
     * if (map.get(key) == null) {
     *     V newValue = mappingFunction.apply(key);
     *     if (newValue != null)
     *         map.put(key, newValue);
     * }
     * }</pre>
     *
     * <p>The default implementation makes no guarantees about detecting if the
     * mapping function modifies this map during computation and, if
     * appropriate, reporting an error. Non-concurrent implementations should
     * override this method and, on a best-effort basis, throw a
     * ConcurrentModificationException} if it is detected that the
     * mapping function modifies this map during computation. Concurrent
     * implementations should override this method and, on a best-effort basis,
     * throw an IllegalStateException} if it is detected that the
     * mapping function modifies this map during computation and as a result
     * computation would never complete.
     *
     * <p>The default implementation makes no guarantees about synchronization
     * or atomicity properties of this method. Any implementation providing
     * atomicity guarantees must override this method and document its
     * concurrency properties. In particular, all implementations of
     * subinterface {@link java.util.concurrent.ConcurrentMap} must document
     * whether the mapping function is applied once atomically only if the value
     * is not present.
     *
     * @param IObject $key key with which the specified value is to be associated
     * @param GFunction $mappingFunction the mapping function to compute a value
     *
     * @return IObject the current (existing or computed) value associated with
     *         the specified key, or null if the computed value is null
     */
     public function computeIfAbsent(IObject $key, GFunction $mappingFunction): IObject;

    /**
     * If the value for the specified key is present and non-null, attempts to
     * compute a new mapping given the key and its current mapped value.
     *
     * <p>If the remapping function returns null}, the mapping is removed.
     * If the remapping function itself throws an (unchecked) exception, the
     * exception is rethrown, and the current mapping is left unchanged.
     *
     * <p>The remapping function should not modify this map during computation.
     *
     * @implSpec
     * The default implementation is equivalent to performing the following
     * steps for this map}, then returning the current value or
     * null} if now absent:
     *
     * <pre> {@code
     * if (map.get(key) != null) {
     *     V oldValue = map.get(key);
     *     V newValue = remappingFunction.apply(key, oldValue);
     *     if (newValue != null)
     *         map.put(key, newValue);
     *     else
     *         map.remove(key);
     * }
     * }</pre>
     *
     * <p>The default implementation makes no guarantees about detecting if the
     * remapping function modifies this map during computation and, if
     * appropriate, reporting an error. Non-concurrent implementations should
     * override this method and, on a best-effort basis, throw a
     * ConcurrentModificationException} if it is detected that the
     * remapping function modifies this map during computation. Concurrent
     * implementations should override this method and, on a best-effort basis,
     * throw an IllegalStateException} if it is detected that the
     * remapping function modifies this map during computation and as a result
     * computation would never complete.
     *
     * <p>The default implementation makes no guarantees about synchronization
     * or atomicity properties of this method. Any implementation providing
     * atomicity guarantees must override this method and document its
     * concurrency properties. In particular, all implementations of
     * sub interface {@link java.util.concurrent.ConcurrentMap} must document
     * whether the remapping function is applied once atomically only if the
     * value is not present.
     *
     * @param IObject $key key with which the specified value is to be associated
     * @param BiFunction $remappingFunction the remapping function to compute a value
     *
     * @return IObject the new value associated with the specified key, or null if none
     */
    public function computeIfPresent(IObject $key, BiFunction $remappingFunction);

    /**
     * Attempts to compute a mapping for the specified key and its current
     * mapped value (or null if there is no current mapping). For
     * example, to either create or append a String msg to a value
     * mapping:
     *
     * <pre> {@code
     * map.compute(key, (k, v) -> (v == null) ? msg : v.concat(msg))}</pre>
     * (Method {@link #merge merge()} is often simpler to use for such purposes.)
     *
     * <p>If the remapping function returns null}, the mapping is removed
     * (or remains absent if initially absent).  If the remapping function
     * itself throws an (unchecked) exception, the exception is rethrown, and
     * the current mapping is left unchanged.
     *
     * <p>The remapping function should not modify this map during computation.
     *
     * @implSpec
     * The default implementation is equivalent to performing the following
     * steps for this map:
     *
     * <pre> {@code
     * V oldValue = map.get(key);
     * V newValue = remappingFunction.apply(key, oldValue);
     * if (newValue != null) {
     *     map.put(key, newValue);
     * } else if (oldValue != null || map.containsKey(key)) {
     *     map.remove(key);
     * }
     * return newValue;
     * }</pre>
     *
     * <p>The default implementation makes no guarantees about detecting if the
     * remapping function modifies this map during computation and, if
     * appropriate, reporting an error. Non-concurrent implementations should
     * override this method and, on a best-effort basis, throw a
     * ConcurrentModificationException} if it is detected that the
     * remapping function modifies this map during computation. Concurrent
     * implementations should override this method and, on a best-effort basis,
     * throw an IllegalStateException} if it is detected that the
     * remapping function modifies this map during computation and as a result
     * computation would never complete.
     *
     * <p>The default implementation makes no guarantees about synchronization
     * or atomicity properties of this method. Any implementation providing
     * atomicity guarantees must override this method and document its
     * concurrency properties. In particular, all implementations of
     * sub interface {@link java.util.concurrent.ConcurrentMap} must document
     * whether the remapping function is applied once atomically only if the
     * value is not present.
     *
     * @param IObject $key key with which the specified value is to be associated
     * @param BiFunction $remappingFunction the remapping function to compute a value
     *
     * @return IObject the new value associated with the specified key, or null if none
     */
    public function compute(IObject $key, BiFunction $remappingFunction);

    /**
     * If the specified key is not already associated with a value or is
     * associated with null, associates it with the given non-null value.
     * Otherwise, replaces the associated value with the results of the given
     * remapping function, or removes if the result is null}. This
     * method may be of use when combining multiple mapped values for a key.
     * For example, to either create or append a String msg} to a
     * value mapping:
     *
     * <pre> {@code
     * map.merge(key, msg, String::concat)
     * }</pre>
     *
     * <p>If the remapping function returns null}, the mapping is removed.
     * If the remapping function itself throws an (unchecked) exception, the
     * exception is rethrown, and the current mapping is left unchanged.
     *
     * <p>The remapping function should not modify this map during computation.
     *
     * @implSpec
     * The default implementation is equivalent to performing the following
     * steps for this map}, then returning the current value or
     * null} if absent:
     *
     * <pre> {@code
     * V oldValue = map.get(key);
     * V newValue = (oldValue == null) ? value :
     *              remappingFunction.apply(oldValue, value);
     * if (newValue == null)
     *     map.remove(key);
     * else
     *     map.put(key, newValue);
     * }</pre>
     *
     * <p>The default implementation makes no guarantees about detecting if the
     * remapping function modifies this map during computation and, if
     * appropriate, reporting an error. Non-concurrent implementations should
     * override this method and, on a best-effort basis, throw a
     * ConcurrentModificationException} if it is detected that the
     * remapping function modifies this map during computation. Concurrent
     * implementations should override this method and, on a best-effort basis,
     * throw an IllegalStateException} if it is detected that the
     * remapping function modifies this map during computation and as a result
     * computation would never complete.
     *
     * <p>The default implementation makes no guarantees about synchronization
     * or atomicity properties of this method. Any implementation providing
     * atomicity guarantees must override this method and document its
     * concurrency properties. In particular, all implementations of
     * subinterface {@link java.util.concurrent.ConcurrentMap} must document
     * whether the remapping function is applied once atomically only if the
     * value is not present.
     *
     * @param IObject $key key with which the resulting value is to be associated
     * @param IObject $value the non-null value to be merged with the existing value
     *        associated with the key or, if no existing value or a null value
     *        is associated with the key, to be associated with the key
     * @param BiFunction $remappingFunction the remapping function to recompute a value if
     *        present
     *
     * @return IObject the new value associated with the specified key, or null if no
     *         value is associated with the key
     */
    public function merge(IObject $key, IObject $value, BiFunction  $remappingFunction);
}