<?php

namespace jhp\util\collection;

use Iterator;
use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\exception\IllegalStateException;
use jhp\lang\exception\UnsupportedOperationException;
use jhp\lang\IObject;
use jhp\lang\TClass;
use jhp\lang\TInteger;
use jhp\util\function\Consumer;
use jhp\util\function\Predicate;
use jhp\util\stream\Stream;

class THashSet extends TIterable implements ISet
{

    private array $set;

    public function __construct(private readonly TClass $type)
    {
        $this->set = [];
    }

    public function size(): int
    {
        return count($this->set, COUNT_RECURSIVE) - count($this->set);
    }

    public function isEmpty(): bool
    {
        return $this->size() === 0;
    }

    public function contains(IObject $o): bool
    {
        $bucket = $this->set[$this->hash($o)];
        if ($bucket == null) {
            return false;
        }

        foreach ($bucket as $entry) {
            if ($entry->equals($o)) {
                return true;
            }
        }
        return false;
    }

    public function toArray(array &$a = []): array
    {
        $i = 0;
        foreach ($this->set as $bucket) {
            foreach ($bucket as $entry) {
                $a[$i++] = $entry;
            }
        }

        $a[$this->size()] = null;
        return $a;
    }

    public function add(IObject $a): bool
    {
        $this->checkObjectType($this->type, $a);

        $hashcode = $this->hash($a);
        $bucket = $this->set[$hashcode] ?? null;

        if ($bucket === null) {
            $this->set[$hashcode] = [$a];
            return true;
        }

        foreach ($bucket as $entry) {
            if (!($entry instanceof IObject)) {
                throw new IllegalStateException("Invalid state of the hashmap");
            }
            if ($entry->equals($a)) {
                return false;
            }
        }

        $bucket[] = $a;
        $this->set[$hashcode] = $bucket;
        return true;
    }

    public function containsAll(ICollection $c): bool
    {
        if (!$this->type->isAssignableFrom($c->getType())) {
            throw new IllegalArgumentException("Tyring to compare a collection of type " . $c->getType()->getName() .
                " to a collection of type " . $this->getType()->getName());
        }

        foreach ($c as $item) {
            $this->contains($item);

        }
    }

    public function addAll(ICollection $a): bool
    {
        if (!$this->type->isAssignableFrom($a->getType())) {
            throw new IllegalArgumentException("Trying to add items from map with type " . $a->getType()->getName() .
                " to map with type " . $this->getType()->getName());
        }

        $set = $this;
        $currentSize = $this->size();

        $a->foreach(Consumer::of(function (IObject $o) use ($set) {
            $set->add($o);
        }));

        return $currentSize !== $this->size();
    }

    public function removeAll(ICollection $c): bool
    {
        // TODO: Implement removeAll() method.
    }

    public function removeObject(IObject $o): bool
    {
        // TODO: Implement removeObject() method.
    }

    public function removeIf(Predicate $filter): bool
    {
        // TODO: Implement removeIf() method.
    }

    public function retainAll(ICollection $c): bool
    {
        // TODO: Implement retainAll() method.
    }

    public function clear(): void
    {
        // TODO: Implement clear() method.
    }

    public function stream(): Stream
    {
        throw new UnsupportedOperationException();
    }

    public function parallelStream(): Stream
    {
        throw new UnsupportedOperationException();
    }

    public function forEach(Consumer $action)
    {
        // TODO: Implement forEach() method.
    }

    public function getType(): TClass
    {
        return $this->type;
    }

    public function iterator(): Iterator
    {
        throw new UnsupportedOperationException();
    }

    private function hash(IObject $key): int {
        $h = $key->hashCode();
        return $h ^ (TInteger::unsignedRightShift($h, 16));
    }

    private function checkObjectType(TClass $class, IObject $objectToBeChecked): void
    {
        if (!$class->isInstance($objectToBeChecked)) {
            throw new IllegalArgumentException(
                "Trying to preform an operation with an object of type: " . TClass::of($objectToBeChecked)->getName() .
                " for an map of type: " . $class->getName()
            );
        }
    }

    public function remove(IObject $o): bool
    {
        return true;
    }
}