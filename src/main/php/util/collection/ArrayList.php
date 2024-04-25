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
 * Copyright (c) 1997, 2019, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */

namespace jhp\util\collection;

use ArrayIterator;
use Iterator;
use jhp\lang\Comparable;
use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\exception\IndexOutOfBoundsException;
use jhp\lang\exception\UnsupportedOperationException;
use jhp\lang\internal\GType;
use jhp\lang\IObject;
use jhp\lang\TClass;
use jhp\util\function\Consumer;
use jhp\util\function\Predicate;
use jhp\util\function\UnaryOperator;
use jhp\util\stream\Stream;

class ArrayList extends AbstractList implements IList
{

    private array $array;

    /**
     * Initializes the arraylist
     *
     * @param TClass           $type The type of the objects that should be stored in this array
     * @param ICollection|null $list A list of items that should be stored in this array, or null if no list should be inserted
     */
    public function __construct(TClass $type, ?ICollection $list = null)
    {
        parent::__construct($type);
        $this->array = [];
        if ($list !== null) {
            $this->addAll($list);
        } else {
            $this->array = [];
        }
    }

    /**
     * Returns the number of elements in this collection.
     * The maximum size of an array is the size of an 32-bit integer in PHP, even if the 64-bit php is used
     *
     * @see https://stackoverflow.com/a/73885850
     *
     * @return int the number of elements in this collection
     */
    public function size(): int
    {
        return count($this->array);
    }

    /**
     * Returns an iterator over the elements in this list in proper sequence.
     *
     * @return Iterator an iterator over the elements in this list in proper sequence
     */
    public function iterator(): Iterator
    {
        return new ArrayIterator($this->array);
    }


    /**
     *  @note
     *  Personally I would recommend against using an array by reference here and instead using the variant with a parameter
     *  But it is part of the Java API, so I implemented it anyway.
     *
     * @param array &$a the array into which the elements of this collection are to be
     *        stored, if it is big enough; otherwise, a new array of the same
     *        runtime type is allocated for this purpose.
     *
     * @return array an array containing all the elements in this collection
     */
    public function toArray(array &$a = []): array
    {
        if ($a === []) {
            $a = $this->array;
            return $a;
        }

        foreach ($this->array as $key => $value) {
            $a[$key] = $value;
        }

        $a[$this->size()] = null;
        return $a;
    }

    /**
     *
     * @apiNote
     * In the JHP library we do not allow null to be added to any collection.
     *
     * @param IObject|int  $a element whose presence in this collection is to be ensured, or the index the object should be inserted on
     * @param IObject|null $b element whose presence in this collection is to be ensured, or null if the object is provided in $a
     *
     * @return bool true if this collection changed as a result of the call
     *
     * @throws IllegalArgumentException if some property of the element
     *         prevents it from being added to this collection
     * @throws IndexOutOfBoundsException if the index is out of range
     *          (index < 0 || index > size())
     */
    public function add(int|IObject $a, ?IObject $b = null): bool
    {
        if (GType::of($a)->isObject() && $b !== null) {
            throw new IllegalArgumentException("B may only be set if A is an int");
        }

        if (GType::of($a)->isInteger() && $b === null) {
            throw new IllegalArgumentException("B should be set if A is an int");
        }

        if ($b === null) {
            $this->checkObjectType($a);
            $this->array[$this->size()] = $a;
        } else {
            if ($a > $this->size()) {
                throw new IndexOutOfBoundsException("Trying to insert an index that is larger than the current size of the array");
            }

            if ($a < 0) {
                throw new IndexOutOfBoundsException("Trying to insert an index that is negative");
            }
            $this->checkObjectType($b);
            array_splice($this->array, $a, 0, [$b]);
        }

        return true;
    }


    /**
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
    public function retainAll(ICollection $c): bool
    {
        $modified = false;
        foreach ($this->array as $value) {
            $index = $this->indexOf($value);
            if (!$c->contains($value)) {
                $this->remove($index);
                $modified = true;
            }
        }
        return $modified;
    }

    /**
     * @param UnaryOperator $operator the operator to apply to each element
     */
    public function replaceAll(UnaryOperator $operator): void
    {
        foreach ($this->array as $key => $value) {
            $this->array[$key] = $operator->apply($value);
        }
    }

    /**
     * @param ?Comparator $c the Comparator used to compare list elements.
     *          A null value indicates that the elements'
     *          {@linkplain Comparable natural ordering} should be used
     *
     * @throws UnsupportedOperationException if the list's list-iterator does
     *         not support the set operation
     * @throws IllegalArgumentException if the comparator is found to violate the {@link Comparator} contract
     */
    public function sort(?Comparator $c = null): void
    {
        if ($c !== null) {
            usort($this->array, fn($a, $b) => $c->compare($a, $b));
            return;
        }

        usort($this->array, function ($a, $b) {
            if (!($a instanceof Comparable) || !($b instanceof Comparable)) {
                throw new IllegalArgumentException("No comparator has been supplied, and not all elements are comparable");
            }
            return $a->compareTo($b);
        });
    }

    /**
     */
    public function clear(): void
    {
        foreach ($this->array as $ignored) {
            // If we remove the 0 index every time eventually the entire array will be cleared
            $this->remove(0);
        }
    }

    /**
     * @throws IndexOutOfBoundsException
     */
    public function get(int $index): object
    {
        if ($index < 0 || $index >= $this->size()) {
            throw new IndexOutOfBoundsException("index is $index, while size is {$this->size()}");
        }

        return $this->array[$index];
    }

    /**
     * @throws IndexOutOfBoundsException
     */
    public function set(int $index, IObject $element): IObject
    {
        $current = $this->remove($index);
        $this->add($index, $element);
        return $current;
    }

    /**
     * @param int $index the index of the element to be removed
     *
     * @return IObject the element previously at the specified position
     *
     * @throws IndexOutOfBoundsException if the index is out of range
     *         (index < 0 || index >= size())
     */
    public function remove(int $index): IObject
    {
        $value = $this->get($index);
        array_splice($this->array, $index, 1);
        return $value;
    }

    /**
     * @param IObject $o element to search for
     *
     * @return int the index of the first occurrence of the specified element in
     *         this list, or -1 if this list does not contain the element
     */
    public function indexOf(IObject $o): int
    {
        foreach ($this->array as $key => $value) {
            if ($value->equals($o)) {
                return $key;
            }
        }

        return -1;
    }

    /**
     * @param IObject $o element to search for
     *
     * @return int the index of the last occurrence of the specified element in
     *         this list, or -1 if this list does not contain the element
     */
    public function lastIndexOf(IObject $o): int
    {
        foreach (array_reverse($this->array) as $key => $value) {
            if ($value->equals($o)) {
                return $this->size() - $key - 1;
            }
        }

        return -1;
    }

    public function listIterator(?int $index = null): ListIterator
    {
        throw new UnsupportedOperationException();
    }

    public function subList(int $fromIndex, int $toIndex): IList
    {
        throw new UnsupportedOperationException();
    }

    /**
     * @implSpec
     * The default implementation traverses all elements of the collection using
     * its {@link #iterator}.  Each matching element is removed using
     * {@link Iterator#remove()}.  If the collection's iterator does not
     * support removal then an UnsupportedOperationException will be
     * thrown on the first matching element.
     *
     * @param Predicate $filter a predicate which returns true for elements to be removed
     *
     * @return true if any elements were removed
     */
    public function removeIf(Predicate $filter): bool
    {
        $modified = false;
        $index = 0;
        foreach ($this->array as $value) {
            if ($filter->test($value)) {
                $modified = true;
                $this->remove($index);
            } else {
                $index++;
            }
        }
        return $modified;
    }

    public function stream(): Stream
    {
        throw new UnsupportedOperationException();
    }

    public function parallelStream(): Stream
    {
        throw new UnsupportedOperationException();
    }

    /**
     * @param Consumer $action The action to be performed for each element
     */
    public function forEach(Consumer $action): void
    {
        foreach ($this->array as $value) {
            $action->accept($value);
        }
    }

    private function checkObjectType(IObject $objectToBeChecked): void
    {
        if (!$this->type->isInstance($objectToBeChecked)) {
            throw new IllegalArgumentException(
                "Trying to preform an operation with an object of type: " . TClass::of($objectToBeChecked)->getName() .
                " for an array list of type: " . $this->type->getName()
            );
        }
    }

}