<?php

namespace jhp\util\collection;


use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\exception\IllegalStateException;
use jhp\lang\exception\IndexOutOfBoundsException;
use jhp\lang\exception\UnsupportedOperationException;
use jhp\lang\IObject;
use jhp\util\function\UnaryOperator;

interface IList extends ICollection
{
    // Modification Operations


    /**
     * @ImplNote
     * In the JHP library we do not allow null to be added to any collection.
     *
     * @param IObject|int $a element whose presence in this collection is to be ensured, or the index the object should be inserted on
     * @param IObject|null $b element whose presence in this collection is to be ensured, or null if the object is provided in $a
     *
     * @return bool true if this collection changed as a result of the call
     *
     * @throws UnsupportedOperationException if the add operation
     *         is not supported by this collection
     * @throws IllegalArgumentException if some property of the element
     *         prevents it from being added to this collection
     */
    public function add(int|IObject $a, ?IObject $b = null): bool;

    // Bulk Modification Operations

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
     *
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
    public function addAll(int|ICollection $a, ICollection $b = null): bool;

    /**
     * Replaces each element of this list with the result of applying the
     * operator to that element.  Errors or runtime exceptions thrown by
     * the operator are relayed to the caller.
     *
     * @implSpec
     * The default implementation is equivalent to, for this list:
     * <pre>
     *     final ListIterator<E> li = list.listIterator();
     *     while (li.hasNext()) {
     *         li.set(operator.apply(li.next()));
     *     }
     * </pre>
     *
     * If the list's list-iterator does not support the set operation
     * then an UnsupportedOperationException will be thrown when
     * replacing the first element.
     *
     * @param UnaryOperator $operator the operator to apply to each element
     *
     * @throws UnsupportedOperationException if this list is unmodifiable.
     *         Implementations may throw this exception if an element
     *         cannot be replaced or if, in general, modification is not
     *         supported
     */
    public function replaceAll(UnaryOperator $operator): void;

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
     * @param Comparator $c the Comparator used to compare list elements.
     *          A null value indicates that the elements'
     *          {@linkplain Comparable natural ordering} should be used
     *
     * @throws UnsupportedOperationException if the list's list-iterator does
     *         not support the set operation
     * @throws IllegalArgumentException if the comparator is found to violate the {@link Comparator} contract
     */
    public function sort(Comparator $c): void;

    // Positional Access Operations

    /**
     * Returns the element at the specified position in this list.
     *
     * @param index index of the element to return
     *
     * @return the element at the specified position in this list
     * @throws IndexOutOfBoundsException if the index is out of range
     *         (index < 0 || index >= size())
     */
    public function get(int $index): mixed;

    /**
     * Replaces the element at the specified position in this list with the
     * specified element (optional operation).
     *
     * @param index index of the element to replace
     * @param element element to be stored at the specified position
     *
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
    public function set(int $index, IObject $element): IObject;

    /**
     * Removes the element at the specified position in this list (optional
     * operation).  Shifts any subsequent elements to the left (subtracts one
     * from their indices).  Returns the element that was removed from the
     * list.
     *
     * @param int $index the index of the element to be removed
     *
     * @return IObject the element previously at the specified position
     * @throws UnsupportedOperationException if the remove operation
     *         is not supported by this list
     * @throws IndexOutOfBoundsException if the index is out of range
     *         (index < 0 || index >= size())
     */
    public function remove(int $index): IObject;


    // Search Operations

    /**
     * Returns the index of the first occurrence of the specified element
     * in this list, or -1 if this list does not contain the element.
     * More formally, returns the lowest index i such that
     * Objects.equals(o, get(i)),
     * or -1 if there is no such index.
     *
     * @param IObject $o element to search for
     *
     * @return int the index of the first occurrence of the specified element in
     *         this list, or -1 if this list does not contain the element
     */
    public function indexOf(IObject $o): int;

    /**
     * Returns the index of the last occurrence of the specified element
     * in this list, or -1 if this list does not contain the element.
     * More formally, returns the highest index i such that
     * Objects.equals(o, get(i)),
     * or -1 if there is no such index.
     *
     * @param IObject $o element to search for
     *
     * @return int the index of the last occurrence of the specified element in
     *         this list, or -1 if this list does not contain the element
     */
    public function lastIndexOf(IObject $o): int;


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
     *
     * @return a list iterator over the elements in this list (in proper
     *         sequence), starting at the specified position in the list
     * @throws IndexOutOfBoundsException if the index is out of range
     *         (index < 0 || index > size())
     */
    public function listIterator(?int $index = null): ListIterator;

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
     *
     * @return a view of the specified range within this list
     * @throws IndexOutOfBoundsException for an illegal endpoint index value
     *         ({@code fromIndex < 0 || toIndex > size ||
     *         fromIndex > toIndex})
     */
    public function subList(int $fromIndex, int $toIndex): IList;
}