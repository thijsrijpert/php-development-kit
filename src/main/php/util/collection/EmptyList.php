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
use jhp\lang\exception\IndexOutOfBoundsException;
use jhp\lang\exception\UnsupportedOperationException;
use jhp\lang\IObject;
use jhp\lang\TClass;
use jhp\util\function\Consumer;
use jhp\util\function\Predicate;
use jhp\util\function\UnaryOperator;
use jhp\util\stream\Stream;

class EmptyList extends AbstractList implements IList
{

    public function size(): int
    {
        return 0;
    }

    public function toArray(array &$a = []): array
    {
        $a = [];
        return $a;
    }

    public function removeIf(Predicate $filter): bool
    {
        return false;
    }

    public function retainAll(ICollection $c): bool
    {
        return false;
    }

    public function clear(): void {}

    public function stream(): Stream
    {
        throw new UnsupportedOperationException();
    }

    public function parallelStream(): Stream
    {
        throw new UnsupportedOperationException();
    }

    public function forEach(Consumer $action): void { }

    public function iterator(): Iterator
    {
        return new ArrayIterator([]);
    }

    public function add(IObject|int $a, ?IObject $b = null): bool
    {
        throw new UnsupportedOperationException();
    }

    public function replaceAll(UnaryOperator $operator): void {  }

    public function sort(Comparator $c): void {  }

    public function get(int $index): mixed
    {
        throw new IndexOutOfBoundsException();
    }

    public function set(int $index, IObject $element): IObject
    {
        throw new UnsupportedOperationException();
    }

    public function remove(int $index): IObject
    {
        throw new UnsupportedOperationException();
    }

    public function indexOf(IObject $o): int
    {
        return -1;
    }

    public function lastIndexOf(IObject $o): int
    {
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
}