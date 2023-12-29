<?php

namespace jhp\util\collection;

use Iterator;
use jhp\util\a;
use jhp\util\c;
use jhp\util\ClassCastException;
use jhp\util\e;
use jhp\util\element;
use jhp\util\fromIndex;
use jhp\util\function\UnaryOperator;
use jhp\util\IllegalArgumentException;
use jhp\util\index;
use jhp\util\IndexOutOfBoundsException;
use jhp\util\NullPointerException;
use jhp\util\o;
use jhp\util\operator;
use jhp\util\Spliterator;
use jhp\util\the;
use jhp\util\toIndex;
use jhp\util\UnsupportedOperationException;

interface IList extends ICollection
{
    // Query Operations

    /**
     * Returns the number of elements in this list.  If this list contains
     * more than Integer.MAX_VALUE elements, returns
     * Integer.MAX_VALUE.
     *
     * @return int the number of elements in this list
     */
    function size(): int;

    /**
     * Returns true if this list contains no elements.
     *
     * @return bool true if this list contains no elements
     */
    function isEmpty(): bool;

    /**
     * Returns true if this list contains the specified element.
     * More formally, returns true if and only if this list contains
     * at least one element e such that
     * Objects.equals(o, e).
     *
     * @param o element whose presence in this list is to be tested
     * @return true if this list contains the specified element
     * @throws ClassCastException if the type of the specified element
     *         is incompatible with this list
     * (<a href="ICollection.html#optional-restrictions">optional</a>)
     * @throws NullPointerException if the specified element is null and this
     *         list does not permit null elements
     * (<a href="ICollection.html#optional-restrictions">optional</a>)
     */
    function contains(object $o): bool;

    /**
     * Returns an iterator over the elements in this list in proper sequence.
     *
     * @return Iterator an iterator over the elements in this list in proper sequence
     */
    function iterator(): Iterator;

    /**
     * Returns an array containing all the elements in this list in proper
     * sequence (from first to last element).
     *
     * <p>The returned array will be "safe" in that no references to it are
     * maintained by this list.  (In other words, this method must
     * allocate a new array even if this list is backed by an array).
     * The caller is thus free to modify the returned array.
     *
     * <p>This method acts as bridge between array-based and collection-based
     * APIs.
     *
     * @return array an array containing all the elements in this list in proper
     *         sequence
     * @see Arrays#asList(Object[])
     */
    function toArray(array &$a = []): array;


    // Modification Operations

    /**
     * Appends the specified element to the end of this list (optional
     * operation).
     *
     * <p>Lists that support this operation may place limitations on what
     * elements may be added to this list.  In particular, some
     * lists will refuse to add null elements, and others will impose
     * restrictions on the type of elements that may be added.  List
     * classes should clearly specify in their documentation any restrictions
     * on what elements may be added.
     *
     * @param e element to be appended to this list
     * @return true (as specified by {@link ICollection#add})
     * @throws UnsupportedOperationException if the add operation
     *         is not supported by this list
     * @throws ClassCastException if the class of the specified element
     *         prevents it from being added to this list
     * @throws NullPointerException if the specified element is null and this
     *         list does not permit null elements
     * @throws IllegalArgumentException if some property of this element
     *         prevents it from being added to this list
     */
    function add(int|object $a, ?object $b = null): bool;

    // Bulk Modification Operations

    /**
     * Returns true if this list contains all of the elements of the
     * specified collection.
     *
     * @param  c collection to be checked for containment in this list
     * @return true if this list contains all of the elements of the
     *         specified collection
     * @throws ClassCastException if the types of one or more elements
     *         in the specified collection are incompatible with this
     *         list
     * (<a href="ICollection.html#optional-restrictions">optional</a>)
     * @throws NullPointerException if the specified collection contains one
     *         or more null elements and this list does not permit null
     *         elements
     *         (<a href="ICollection.html#optional-restrictions">optional</a>),
     *         or if the specified collection is null
     * @see #contains(Object)
     */
    function containsAll(ICollection $c): bool;

    /**
     * Inserts all the elements in the specified collection into this
     * list at the specified position (optional operation).  Shifts the
     * element currently at that position (if any) and any subsequent
     * elements to the right (increases their indices).  The new elements
     * will appear in this list in the order that they are returned by the
     * specified collection's iterator.  The behavior of this operation is
     * undefined if the specified collection is modified while the
     * operation is in progress.  (Note that this will occur if the specified
     * collection is this list, and it's nonempty.)
     *
     * @param index index at which to insert the first element from the
     *              specified collection
     * @param c collection containing elements to be added to this list
     * @return true if this list changed as a result of the call
     * @throws UnsupportedOperationException if the addAll operation
     *         is not supported by this list
     * @throws ClassCastException if the class of an element of the specified
     *         collection prevents it from being added to this list
     * @throws NullPointerException if the specified collection contains one
     *         or more null elements and this list does not permit null
     *         elements, or if the specified collection is null
     * @throws IllegalArgumentException if some property of an element of the
     *         specified collection prevents it from being added to this list
     * @throws IndexOutOfBoundsException if the index is out of range
     *         (index < 0 || index > size())
     */
    function addAll(int|ICollection $a, ICollection $b = null): bool;

    /**
     * Removes from this list all of its elements that are contained in the
     * specified collection (optional operation).
     *
     * @param c collection containing elements to be removed from this list
     * @return true if this list changed as a result of the call
     * @throws UnsupportedOperationException if the removeAll operation
     *         is not supported by this list
     * @throws ClassCastException if the class of an element of this list
     *         is incompatible with the specified collection
     * (<a href="ICollection.html#optional-restrictions">optional</a>)
     * @throws NullPointerException if this list contains a null element and the
     *         specified collection does not permit null elements
     *         (<a href="ICollection.html#optional-restrictions">optional</a>),
     *         or if the specified collection is null
     * @see #remove(Object)
     * @see #contains(Object)
     */
    function removeAll(ICollection $c): bool;

    /**
     * Retains only the elements in this list that are contained in the
     * specified collection (optional operation).  In other words, removes
     * from this list all of its elements that are not contained in the
     * specified collection.
     *
     * @param c collection containing elements to be retained in this list
     * @return true if this list changed as a result of the call
     * @throws UnsupportedOperationException if the retainAll operation
     *         is not supported by this list
     * @throws ClassCastException if the class of an element of this list
     *         is incompatible with the specified collection
     * (<a href="ICollection.html#optional-restrictions">optional</a>)
     * @throws NullPointerException if this list contains a null element and the
     *         specified collection does not permit null elements
     *         (<a href="ICollection.html#optional-restrictions">optional</a>),
     *         or if the specified collection is null
     * @see #remove(Object)
     * @see #contains(Object)
     */
    function retainAll(ICollection $c): bool;

    /**
     * Replaces each element of this list with the result of applying the
     * operator to that element.  Errors or runtime exceptions thrown by
     * the operator are relayed to the caller.
     *
     * @implSpec
     * The default implementation is equivalent to, for this list:
     * <pre>{@code
     *     final ListIterator<E> li = list.listIterator();
     *     while (li.hasNext()) {
     *         li.set(operator.apply(li.next()));
     *     }
     * }</pre>
     *
     * If the list's list-iterator does not support the set operation
     * then an UnsupportedOperationException will be thrown when
     * replacing the first element.
     *
     * @param operator the operator to apply to each element
     * @throws UnsupportedOperationException if this list is unmodifiable.
     *         Implementations may throw this exception if an element
     *         cannot be replaced or if, in general, modification is not
     *         supported
     * @throws NullPointerException if the specified operator is null or
     *         if the operator result is a null value and this list does
     *         not permit null elements
     *         (<a href="ICollection.html#optional-restrictions">optional</a>)
     * @since 1.8
     */
    function replaceAll(UnaryOperator $operator): void;

    /**
     * Sorts this list according to the order induced by the specified
     * {@link Comparator}.  The sort is <i>stable</i>: this method must not
     * reorder equal elements.
     *
     * <p>All elements in this list must be <i>mutually comparable</i> using the
     * specified comparator (that is, c.compare(e1, e2) must not throw
     * a ClassCastException for any elements e1 and e2
     * in the list).
     *
     * <p>If the specified comparator is null then all elements in this
     * list must implement the {@link Comparable} interface and the elements'
     * {@linkplain Comparable natural ordering} should be used.
     *
     * <p>This list must be modifiable, but need not be resizable.
     *
     * @implSpec
     * The default implementation obtains an array containing all elements in
     * this list, sorts the array, and iterates over this list resetting each
     * element from the corresponding position in the array. (This avoids the
     * n<sup>2</sup> log(n) performance that would result from attempting
     * to sort a linked list in place.)
     *
     * @implNote
     * This implementation is a stable, adaptive, iterative mergesort that
     * requires far fewer than n lg(n) comparisons when the input array is
     * partially sorted, while offering the performance of a traditional
     * mergesort when the input array is randomly ordered.  If the input array
     * is nearly sorted, the implementation requires approximately n
     * comparisons.  Temporary storage requirements vary from a small constant
     * for nearly sorted input arrays to n/2 object references for randomly
     * ordered input arrays.
     *
     * <p>The implementation takes equal advantage of ascending and
     * descending order in its input array, and can take advantage of
     * ascending and descending order in different parts of the same
     * input array.  It is well-suited to merging two or more sorted arrays:
     * simply concatenate the arrays and sort the resulting array.
     *
     * <p>The implementation was adapted from Tim Peters's list sort for Python
     * (<a href="http://svn.python.org/projects/python/trunk/Objects/listsort.txt">
     * TimSort</a>).  It uses techniques from Peter McIlroy's "Optimistic
     * Sorting and Information Theoretic Complexity", in Proceedings of the
     * Fourth Annual ACM-SIAM Symposium on Discrete Algorithms, pp 467-474,
     * January 1993.
     *
     * @param c the Comparator used to compare list elements.
     *          A null value indicates that the elements'
     *          {@linkplain Comparable natural ordering} should be used
     * @throws ClassCastException if the list contains elements that are not
     *         <i>mutually comparable</i> using the specified comparator
     * @throws UnsupportedOperationException if the list's list-iterator does
     *         not support the set operation
     * @throws IllegalArgumentException
     *         (<a href="ICollection.html#optional-restrictions">optional</a>)
     *         if the comparator is found to violate the {@link Comparator}
     *         contract
     * @since 1.8
     */
    function sort(Comparator $c): void;

    /**
     * Removes all the elements from this list (optional operation).
     * The list will be empty after this call returns.
     *
     * @throws UnsupportedOperationException if the clear operation
     *         is not supported by this list
     */
    function clear(): void;


    // Comparison and hashing

    /**
     * Compares the specified object with this list for equality.  Returns
     * true if and only if the specified object is also a list, both
     * lists have the same size, and all corresponding pairs of elements in
     * the two lists are <i>equal</i>.  (Two elements e1 and
     * e2 are <i>equal</i> if Objects.equals(e1, e2).)
     * In other words, two lists are defined to be
     * equal if they contain the same elements in the same order.  This
     * definition ensures that the equals method works properly across
     * different implementations of the List interface.
     *
     * @param o the object to be compared for equality with this list
     * @return true if the specified object is equal to this list
     */
    function equals(object $o): bool;


    // Positional Access Operations

    /**
     * Returns the element at the specified position in this list.
     *
     * @param index index of the element to return
     * @return the element at the specified position in this list
     * @throws IndexOutOfBoundsException if the index is out of range
     *         (index < 0 || index >= size())
     */
    function get(int $index): mixed;

    /**
     * Replaces the element at the specified position in this list with the
     * specified element (optional operation).
     *
     * @param index index of the element to replace
     * @param element element to be stored at the specified position
     * @return the element previously at the specified position
     * @throws UnsupportedOperationException if the set operation
     *         is not supported by this list
     * @throws ClassCastException if the class of the specified element
     *         prevents it from being added to this list
     * @throws NullPointerException if the specified element is null and
     *         this list does not permit null elements
     * @throws IllegalArgumentException if some property of the specified
     *         element prevents it from being added to this list
     * @throws IndexOutOfBoundsException if the index is out of range
     *         (index < 0 || index >= size())
     */
    function set(int $index, object $element): object;

    /**
     * Removes the element at the specified position in this list (optional
     * operation).  Shifts any subsequent elements to the left (subtracts one
     * from their indices).  Returns the element that was removed from the
     * list.
     *
     * @param index the index of the element to be removed
     * @return the element previously at the specified position
     * @throws UnsupportedOperationException if the remove operation
     *         is not supported by this list
     * @throws IndexOutOfBoundsException if the index is out of range
     *         (index < 0 || index >= size())
     */
    function remove(int $index): object;


    // Search Operations

    /**
     * Returns the index of the first occurrence of the specified element
     * in this list, or -1 if this list does not contain the element.
     * More formally, returns the lowest index i such that
     * Objects.equals(o, get(i)),
     * or -1 if there is no such index.
     *
     * @param o element to search for
     * @return the index of the first occurrence of the specified element in
     *         this list, or -1 if this list does not contain the element
     * @throws ClassCastException if the type of the specified element
     *         is incompatible with this list
     *         (<a href="ICollection.html#optional-restrictions">optional</a>)
     * @throws NullPointerException if the specified element is null and this
     *         list does not permit null elements
     *         (<a href="ICollection.html#optional-restrictions">optional</a>)
     */
    function indexOf(Object $o): int;

    /**
     * Returns the index of the last occurrence of the specified element
     * in this list, or -1 if this list does not contain the element.
     * More formally, returns the highest index i such that
     * Objects.equals(o, get(i)),
     * or -1 if there is no such index.
     *
     * @param o element to search for
     * @return the index of the last occurrence of the specified element in
     *         this list, or -1 if this list does not contain the element
     * @throws ClassCastException if the type of the specified element
     *         is incompatible with this list
     *         (<a href="ICollection.html#optional-restrictions">optional</a>)
     * @throws NullPointerException if the specified element is null and this
     *         list does not permit null elements
     *         (<a href="ICollection.html#optional-restrictions">optional</a>)
     */
    function lastIndexOf(object $o): int;


    // List Iterators

    /**
     * Returns a list iterator over the elements in this list (in proper
     * sequence), starting at the specified position in the list.
     * The specified index indicates the first element that would be
     * returned by an initial call to {@link ListIterator#next next}.
     * An initial call to {@link ListIterator#previous previous} would
     * return the element with the specified index minus one.
     *
     * @param index index of the first element to be returned from the
     *        list iterator (by a call to {@link ListIterator#next next})
     * @return a list iterator over the elements in this list (in proper
     *         sequence), starting at the specified position in the list
     * @throws IndexOutOfBoundsException if the index is out of range
     *         (index < 0 || index > size())
     */
    function listIterator(?int $index = null): ListIterator;

    // View

    /**
     * Returns a view of the portion of this list between the specified
     * fromIndex, inclusive, and toIndex, exclusive.  (If
     * fromIndex and toIndex are equal, the returned list is
     * empty.)  The returned list is backed by this list, so non-structural
     * changes in the returned list are reflected in this list, and vice-versa.
     * The returned list supports all the optional list operations supported
     * by this list.<p>
     *
     * This method eliminates the need for explicit range operations (of
     * the sort that commonly exist for arrays).  Any operation that expects
     * a list can be used as a range operation by passing a subList view
     * instead of a whole list.  For example, the following idiom
     * removes a range of elements from a list:
     * <pre>{@code
     *      list.subList(from, to).clear();
     * }</pre>
     * Similar idioms may be constructed for indexOf and
     * lastIndexOf, and all the algorithms in the
     * Collections class can be applied to a subList.<p>
     *
     * The semantics of the list returned by this method become undefined if
     * the backing list (i.e., this list) is <i>structurally modified</i> in
     * any way other than via the returned list.  (Structural modifications are
     * those that change the size of this list, or otherwise perturb it in such
     * a fashion that iterations in progress may yield incorrect results.)
     *
     * @param fromIndex low endpoint (inclusive) of the subList
     * @param toIndex high endpoint (exclusive) of the subList
     * @return a view of the specified range within this list
     * @throws IndexOutOfBoundsException for an illegal endpoint index value
     *         ({@code fromIndex < 0 || toIndex > size ||
     *         fromIndex > toIndex})
     */
    function subList(int $fromIndex, int $toIndex): IList;

    /**
     * Creates a {@link Spliterator} over the elements in this list.
     *
     * <p>The Spliterator reports {@link Spliterator#SIZED} and
     * {@link Spliterator#ORDERED}.  Implementations should document the
     * reporting of additional characteristic values.
     *
     * @implSpec
     * The default implementation creates a
     * <em><a href="Spliterator.html#binding">late-binding</a></em>
     * spliterator as follows:
     * <ul>
     * <li>If the list is an instance of {@link RandomAccess} then the default
     *     implementation creates a spliterator that traverses elements by
     *     invoking the method {@link List#get}.  If such invocation results or
     *     would result in an IndexOutOfBoundsException then the
     *     spliterator will <em>fail-fast</em> and throw a
     *     ConcurrentModificationException.
     *     If the list is also an instance of {@link AbstractList} then the
     *     spliterator will use the list's {@link AbstractList#modCount modCount}
     *     field to provide additional <em>fail-fast</em> behavior.
     * <li>Otherwise, the default implementation creates a spliterator from the
     *     list's Iterator.  The spliterator inherits the
     *     <em>fail-fast</em> of the list's iterator.
     * </ul>
     *
     * @implNote
     * The created Spliterator additionally reports
     * {@link Spliterator#SUBSIZED}.
     *
     * @return a Spliterator over the elements in this list
     * @since 1.8
     */
    function spliterator(): Spliterator;
}