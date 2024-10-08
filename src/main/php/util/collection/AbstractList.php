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

use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\exception\IndexOutOfBoundsException;
use jhp\lang\internal\GType;
use jhp\lang\IObject;
use jhp\lang\TClass;

abstract class AbstractList extends AbstractCollection implements IList
{

    /**
     * @param TClass $type The type of the objects that should be stored in this array
     */
    public function __construct(TClass $type)
    {
        parent::__construct($type);
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
                $this->addAt($a++, $value);
            }
        }
        return true;
    }

    /**
     * @param   ?IObject $obj the reference object with which to compare.
     *
     * @return  bool true if this object is the same as the obj argument; false otherwise.
     * @api
     * It is generally necessary to override the {@link IObject::hashCode}
     * method whenever this method is overridden, to maintain the
     * general contract for the hashCode method, which states
     * that equal objects must have equal hash codes.
     *
     * @see     IObject::hashCode()
     * @see     THashMap
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
        $this->addAt($offset, $value);
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

        $this->removeAt($offset);
    }

    public function remove(IObject $o): bool
    {
        $index = $this->indexOf($o);
        if ($index === -1) {
            return false;
        }
        $this->removeAt($index);
        return true;
    }
}