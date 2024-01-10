<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 */
/*
 * Copyright (c) 1997, 2018, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 *
 * This code is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License version 2 only, as
 * published by the Free Software Foundation.  Oracle designates this
 * particular file as subject to the "Classpath" exception as provided
 * by Oracle in the LICENSE file that accompanied this code.
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
 *
 * Please contact Oracle, 500 Oracle Parkway, Redwood Shores, CA 94065 USA
 * or visit www.oracle.com if you need additional information or have any
 * questions.
 */

namespace jhp\util\collection;

use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\exception\IndexOutOfBoundsException;
use jhp\lang\internal\GType;
use jhp\lang\IObject;
use jhp\lang\TClass;
use jhp\lang\TObject;
use Traversable;

abstract class AbstractList extends TObject implements IList
{
    protected readonly TClass $type;

    public function __construct(TClass $type)
    {
        $this->type = $type;
    }

    /**
     * Returns true if this list contains no elements.
     *
     * @return bool true if this list contains no elements
     */
    public function isEmpty(): bool
    {
        return $this->size() === 0;
    }

    /**
     * Returns true if this collection contains the specified element.
     * More formally, returns true if and only if this collection
     * contains at least one element e such that
     * Objects.equals(o, e).
     *
     * @param IObject $o element whose presence in this collection is to be tested
     *
     * @return bool true if this collection contains the specified element
     */
    public function contains(IObject $o): bool
    {
        return $this->indexOf($o) >= 0;
    }

    /**
     * Gets the type of the elements contained in this collection
     *
     * @return TClass the type of the elements in this collection
     */
    public function getType(): TClass
    {
        return $this->type;
    }

    /**
     * @param ICollection $c collection to be checked for containment in this collection
     *
     * @return true if this collection contains all the elements in the specified collection
     *
     * @throws IllegalArgumentException if the element is not of the same type as the list
     *
     * @see    ICollection::contains()
     */
    public function containsAll(ICollection $c): bool
    {
        foreach ($c as $value) {
            if (!$this->contains($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param ICollection $c collection containing elements to be removed from this collection
     *
     * @return true if this collection changed as a result of the call
     *
     * @throws IllegalArgumentException if the types of one or more elements
     *         in this collection are incompatible with the specified
     *         collection
     * @see ICollection::remove(Object)
     * @see ICollection::contains(Object)
     */
    public function removeAll(ICollection $c): bool
    {
        $modified = false;
        foreach ($c as $value) {
            $index = $this->indexOf($value);
            if ($index != -1) {
                $this->remove($index);
                $modified = true;
            }
        }
        return $modified;
    }


    /**
     * @param ICollection|int  $a element whose presence in this collection is to be ensured, or the index the object should be inserted on
     * @param ICollection|null $b element whose presence in this collection is to be ensured, or null if the object is provided in $a
     *
     * @return true if this list changed as a result of the call
     * @throws IllegalArgumentException if some property of an element of the
     *         specified collection prevents it from being added to this list
     * @throws IndexOutOfBoundsException if the index is out of range
     *         (index < 0 || index > size())
     */
    public function addAll(int|ICollection $a, ?ICollection $b = null): bool
    {
        if (GType::of($a) === GType::OBJECT && $b !== null) {
            throw new IllegalArgumentException("B may only be set if A is an int");
        }

        if (GType::of($a)->isInteger() && $b === null) {
            throw new IllegalArgumentException("B should be set if A is an int");
        }

        if ($b === null) {
            if ($a->size() === 0) {
                return false;
            }
            if (!$this->type->isAssignableFrom($a->getType())) {
                throw new IllegalArgumentException("Trying to add items from collection with type " . $a->getType()->getName() .
                    " to collection of type " . $this->getType()->getName());
            }
            foreach ($a as $value) {
                $this->add($value);
            }
        } else {
            if ($b->size() === 0) {
                return false;
            }
            if (!$this->type->isAssignableFrom($b->getType())) {
                throw new IllegalArgumentException("Trying to add items from collection with type " . $a->getType()->getName() .
                    " to collection of type " . $this->getType()->getName());
            }
            foreach ($b as $value) {
                $this->add($a++, $value);
            }
        }
        return true;
    }

    /**
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
    public function equals(?IObject $obj = null): bool
    {
        if (!($obj instanceof ArrayList) || $obj->size() !== $this->size()) {
            return false;
        }

        for($i = 0; $i < $this->size(); $i++) {
            $item = $this->get($i);
            $otherItem = $obj->get($i);
            if (!($item instanceof IObject) || !($otherItem instanceof IObject)) {
                return false;
            }
            if (!$item->equals($otherItem)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param mixed $offset The offset to retrieve.
     *
     * @return IObject The value at the index
     */
    public function offsetGet(mixed $offset): IObject
    {
        if (!GType::of($offset)->isInteger()) {
            throw new IllegalArgumentException("Lists use int as the index type");
        }
        return $this->get($offset);
    }


    /**
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!GType::of($value)->isObject()) {
            throw new IllegalArgumentException("Trying to add a non-object to an array list");
        }
        if (!GType::of($offset)->isInteger()) {
            throw new IllegalArgumentException("Lists use int as an index");
        }
        $this->add($offset, $value);
    }

    /**
     * Whether an offset exists
     * @param mixed $offset An offset to check for.
     * @return bool true on success or false on failure.
     */
    public function offsetExists(mixed $offset): bool
    {
        if(!GType::of($offset)->isInteger()
            || $offset < 0
            || $offset >= $this->size()) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed $offset The offset to unset.
     */
    public function offsetUnset(mixed $offset): void
    {
        if (!GType::of($offset)->isInteger()) {
            throw new IllegalArgumentException("Lists use int as an index");
        }

        $this->remove($offset);
    }

    /**
     * @return Traversable An instance of an object implementing <b>Iterator</b> or <b>Traversable</b>
     */
    public function getIterator(): Traversable
    {
        return $this->iterator();
    }
}