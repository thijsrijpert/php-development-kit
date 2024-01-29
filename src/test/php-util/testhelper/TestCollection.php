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
namespace jhp\testhelper;

use ArrayIterator;
use Iterator;
use jhp\lang\exception\UnsupportedOperationException;
use jhp\lang\TClass;
use jhp\lang\TObject;
use jhp\util\collection\ICollection;
use jhp\util\function\Consumer;
use jhp\util\function\Predicate;
use jhp\util\Spliterator;
use jhp\util\stream\Stream;
use Traversable;

class TestCollection extends TObject implements ICollection
{

    public function __construct(
        private readonly TClass $type,
        private readonly array $array) {}

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->array);
    }

    public function offsetExists(mixed $offset): bool
    {
        throw new UnsupportedOperationException();
    }

    public function offsetGet(mixed $offset): mixed
    {
        throw new UnsupportedOperationException();
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new UnsupportedOperationException();
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new UnsupportedOperationException();
    }

    function size(): int
    {
        return count($this->array);
    }

    function isEmpty(): bool
    {
        throw new UnsupportedOperationException();
    }

    function contains(object $o): bool
    {
        throw new UnsupportedOperationException();
    }

    function iterator(): Iterator
    {
        throw new UnsupportedOperationException();
    }

    function toArray(array &$a = []): array
    {
        return $this->array;
    }

    function add(object $a): bool
    {
        throw new UnsupportedOperationException();
    }

    function containsAll(ICollection $c): bool
    {
        throw new UnsupportedOperationException();
    }

    function addAll(ICollection $a): bool
    {
        throw new UnsupportedOperationException();
    }

    function removeAll(ICollection $c): bool
    {
        throw new UnsupportedOperationException();
    }

    function removeIf(Predicate $filter): bool
    {
        throw new UnsupportedOperationException();
    }

    function retainAll(ICollection $c): bool
    {
        throw new UnsupportedOperationException();
    }

    function clear(): void
    {
        throw new UnsupportedOperationException();
    }

    function spliterator(): Spliterator
    {
        throw new UnsupportedOperationException();
    }

    function stream(): Stream
    {
        throw new UnsupportedOperationException();
    }

    function parallelStream(): Stream
    {
        throw new UnsupportedOperationException();
    }

    function forEach(Consumer $action)
    {
        throw new UnsupportedOperationException();
    }

    public function getType(): TClass
    {
        return $this->type;
    }
}