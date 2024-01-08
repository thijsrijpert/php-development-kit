<?php

namespace jhp\lang;

use Iterator;
use IteratorAggregate;
use jhp\util\function\Consumer;
use jhp\util\Spliterator;

/**
 * Implementing this interface allows an object to be the target of the enhanced
 * for statement (sometimes called the "for-each loop" statement).
 */
interface IIterable extends IteratorAggregate
{

    /**
     * Returns an iterator over elements of type T.
     *
     * @return Iterator Iterator.
     */
    public function iterator(): Iterator;

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
     * Creates a {@link Spliterator} over the elements described by this
     * Iterable.
     *
     * @implSpec
     * The default implementation creates an
     * <em><a href="../util/Spliterator.html#binding">early-binding</a></em>
     * spliterator from the iterable's Iterator.  The spliterator
     * inherits the <em>fail-fast</em> properties of the iterable's iterator.
     *
     * @implNote
     * The default implementation should usually be overridden.  The
     * spliterator returned by the default implementation has poor splitting
     * capabilities, is unsized, and does not report any spliterator
     * characteristics. Implementing classes can nearly always provide a
     * better implementation.
     *
     * @return Spliterator a Spliterator over the elements described by this
     * Iterable.
     */
    public function spliterator(): Spliterator;
}
