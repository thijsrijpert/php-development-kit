<?php

namespace jhp\util\collection;

use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\exception\IndexOutOfBoundsException;
use jhp\lang\exception\UnsupportedOperationException;
use jhp\lang\TClass;
use jhp\util\function\Consumer;

abstract class AbstractCollection extends TIterable implements ICollection
{

    protected readonly TClass $type;

    public function __construct(TClass $type)
    {
        $this->type = $type;
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
     * Returns true if this list contains no elements.
     *
     * @return bool true if this list contains no elements
     */
    public function isEmpty(): bool
    {
        return $this->size() === 0;
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
     * @param ICollection $a list of items that should be inserted
     *
     * @return true if this list changed as a result of the call
     * @throws IllegalArgumentException if some property of an element of the
     *         specified collection prevents it from being added to this list
     * @throws IndexOutOfBoundsException if the index is out of range
     *         (index < 0 || index > size())
     */
    public function addAll(ICollection $a): bool
    {

        if ($a->size() === 0) {
            return false;
        }

        if (!$this->type->isAssignableFrom($a->getType())) {
            throw new IllegalArgumentException("Trying to add items from collection with type " . $a->getType()->getName() .
                " to collection of type " . $this->getType()->getName());
        }

        foreach ($a as $item) {
            $this->add($item);
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
        foreach ($c as $item) {
            $modified = $this->remove($item) || $modified;
        }
        return $modified;
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
        foreach ($this as $value) {
            if (!$c->contains($value)) {
                $this->remove($value);
                $modified = true;
            }
        }
        return $modified;
    }

    /**
     * @param Consumer $action The action to be performed for each element
     */
    public function forEach(Consumer $action): void
    {
        foreach ($this as $value) {
            $action->accept($value);
        }
    }
}