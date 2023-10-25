<?php

namespace jhp\util;

use ArrayIterator;
use Iterator;
use jhp\lang\Clazz;
use jhp\lang\GType;
use jhp\lang\IndexOutOfBoundsException;
use jhp\util\function\Consumer;
use jhp\util\function\internal\IllegalArgumentException;
use jhp\util\function\internal\NullPointerException;
use jhp\util\function\Predicate;
use jhp\util\function\UnaryOperator;
use jhp\util\stream\Stream;
use Traversable;
use TypeError;


class ArrayList implements IList
{

    public function __construct(
        private readonly Clazz $type,
        private array $array = []) {
        foreach ($array as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    function size(): int
    {
        return count($this->array);
    }

    function isEmpty(): bool
    {
        return count($this->array) === 0;
    }

    function contains(object $o): bool
    {
        if (Clazz::of($o)->getName() !== $this->type->getName()) {
            return false;
        }

        if (in_array($o, $this->array)) {
            return true;
        }
        return false;
    }

    function iterator(): Iterator
    {
        return new ArrayIterator($this->array);
    }

    function toArray(array &$a = []): array
    {
        foreach ($this->array as $key => $value) {
            $a[$key] = $value;
        }
        return $a;
    }

    function add(int|object $a, ?object $b = null): bool
    {
        if (GType::of($a) === GType::OBJECT && $b !== null) {
            throw new IllegalArgumentException("B maybe only be set if A is an int");
        }

        if ($b === null) {
            $this->checkObjectType($a);
            $this->array[$this->size()] = $a;
        } else {
            $this->checkObjectType($b);
            array_splice( $this->array, $a - 1, 0, $b );
        }

        return true;
    }

    function containsAll(ICollection $c): bool
    {
        foreach ($c as $value) {
            if (!$this->contains($value)) {
                return false;
            }
        }
        return true;
    }

    function addAll(int|ICollection $a, ?ICollection $b = null): bool
    {
        if (GType::of($a) === GType::OBJECT && $b !== null) {
            throw new IllegalArgumentException("B maybe only be set if A is an int");
        }

        if ($b === null) {
            foreach ($a as $value) {
                $this->add($value);
            }
        } else {
            foreach ($b as $value) {
                $this->add($a, $value);
            }
        }
        return true;
    }

    function removeAll(ICollection $c): bool
    {
        // TODO: Implement removeAll() method.
    }

    function retainAll(ICollection $c): bool
    {
        // TODO: Implement retainAll() method.
    }

    function replaceAll(UnaryOperator $operator): void
    {
        // TODO: Implement replaceAll() method.
    }

    function sort(Comparator $c): void
    {
        // TODO: Implement sort() method.
    }

    function clear(): void
    {
        // TODO: Implement clear() method.
    }

    function equals(object $o): bool
    {
        // TODO: Implement equals() method.
    }

    function hashCode(): int
    {
        // TODO: Implement hashCode() method.
    }

    /**
     * @throws IndexOutOfBoundsException
     */
    function get(int $index): object
    {
        if ($index < 0 || $index >= $this->size()) {
            throw new IndexOutOfBoundsException("index is $index, while size is {$this->size()}");
        }

        return $this->array[$index];
    }

    /**
     * @throws IndexOutOfBoundsException
     */
    function set(int $index, object $element): object
    {
        $current = $this->get($index);
        $this->array[$index] = $element;
        return $current;
    }

    function remove(int $index): object
    {
        $value = $this->get($index);
        array_splice($this->array, $index, 1);
        return $value;
    }

    function indexOf(object $o): int
    {
        foreach ($this->array as $key => $value) {
            if ($o == $value) {
                return $key;
            }
        }

        return -1;
    }

    function lastIndexOf(object $o): int
    {
        foreach (array_reverse($this->array) as $key => $value) {
            if ($o == $value) {
                return $this->size() - $key - 1;
            }
        }

        return -1;
    }

    function listIterator(?int $index = null): ListIterator
    {
        throw new NullPointerException();
    }

    function subList(int $fromIndex, int $toIndex): IList
    {
        throw new NullPointerException();
    }

    function spliterator(): Spliterator
    {
        throw new NullPointerException();
    }

    function removeIf(Predicate $filter): bool
    {
        $modified = false;
        foreach($this->array as $index => $value) {
            if ($filter->test($value)) {
                $modified = true;
                $this->remove($index);
            }
        }
        return $modified;
    }

    function stream(): Stream
    {
        throw new NullPointerException();
    }

    function parallelStream(): Stream
    {
        throw new NullPointerException();
    }

    function forEach(Consumer $action): void
    {
        foreach ($this->array as $value) {
            $action->accept($value);
        }
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->array);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->array[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->array[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!GType::of($value)->isObject()) {
            throw new TypeError("Trying to add a non-object to an array list");
        }
        $this->checkObjectType($value);

        $this->array[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->array[$offset]);
    }

    private function checkObjectType(object $objectToBeAdded): void {
        if (Clazz::of($objectToBeAdded)->equals($this->type)) {
            throw new TypeError("Trying to add an object of type: " . Clazz::of($objectToBeAdded)->getName() .
                "to array list of type: " . $this->type->getName());
        }
    }
}