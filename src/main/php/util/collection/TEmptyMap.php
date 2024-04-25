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
 * Copyright (c) 1997, 2021, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */

namespace jhp\util\collection;

use Iterator;
use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\exception\IllegalStateException;
use jhp\lang\exception\UnsupportedOperationException;
use jhp\lang\internal\GType;
use jhp\lang\IObject;
use jhp\lang\TClass;
use jhp\lang\TInteger;
use jhp\util\function\BiConsumer;
use jhp\util\function\BiFunction;
use jhp\util\function\GFunction;
use RuntimeException;

class TEmptyMap extends TIterable implements IMap
{
    /**
     * @var array $hashmap The hashmap data structure
     * @format The hash codes of the keys are used as the keys in this associative array.
     *         The values of this array are indexed arrays called buckets where
     *         all items that have the same hashcode are stored, as hash codes can be duped.
     *         One item in a bucket is also an Entry consisting of a key and a value.
     * @example
     * <pre>     [
     *     47478 => [
     *         Entry(TObject(123), TInteger(10)),
     *         Entry(TObject(124), TInteger(8)),
     *     ],
     *     74854 => [
     *         Entry(TObject(456), TInteger(87)),
     *         Entry(TObject(458), TInteger(25)),
     *    ]
     * ] </pre>
     */
    private array $hashmap = [];
    private readonly TClass $keyType;
    private readonly TClass $valueType;

    public function __construct(TClass $keyType, TClass $valueType) {
        $this->keyType = $keyType;
        $this->valueType = $valueType;
    }

    public function getKeyType(): TClass
    {
        return $this->keyType;
    }

    public function getValueType(): TClass
    {
        return $this->valueType;
    }

    public function get(IObject $key): ?IObject {
        return null;
    }

    public function size(): int
    {
        return 0;
    }

    public function isEmpty(): bool
    {
        return true;
    }

    public function containsKey(IObject $key): bool
    {
        return false;
    }

    public function containsValue(IObject $value): bool
    {
        return false;
    }

    public function put(IObject $key, IObject $value): ?IObject
    {
        throw new UnsupportedOperationException();
    }

    public function putAll(IMap $m): void
    {
        throw new UnsupportedOperationException();
    }

    public function clear(): void
    {

    }

    public function keySet(): Set
    {
        throw new UnsupportedOperationException();
    }

    public function values(): ICollection
    {
        throw new UnsupportedOperationException();
    }

    public function entrySet(): Set
    {
        throw new UnsupportedOperationException();
    }

    public function getOrDefault(IObject $key, IObject $defaultValue): IObject
    {
        return $defaultValue;
    }

    public function forEach(BiConsumer $action): void
    {
    }

    public function replaceAll(BiFunction $function): void
    {

    }

    public function putIfAbsent(IObject $key, IObject $value): IObject
    {
        throw new UnsupportedOperationException();
    }

    public function remove(IObject $key, ?IObject $value = null): ?IObject
    {
        return null;
    }

    public function replace(IObject $key, IObject $value, ?IObject $newValue = null): ?IObject
    {
        return null;
    }

    public function computeIfAbsent(IObject $key, GFunction $mappingFunction): IObject
    {
        throw new UnsupportedOperationException();
    }

    public function computeIfPresent(IObject $key, BiFunction $remappingFunction)
    {
        throw new UnsupportedOperationException();
    }

    public function compute(IObject $key, BiFunction $remappingFunction)
    {
        throw new UnsupportedOperationException();
    }

    public function merge(IObject $key, IObject $value, BiFunction $remappingFunction)
    {
        throw new UnsupportedOperationException();
    }

    public function offsetExists(mixed $offset): bool
    {
        return false;
    }

    public function offsetGet(mixed $offset): null
    {
        return null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new UnsupportedOperationException();
    }

    public function offsetUnset(mixed $offset): void
    {
    }

    public function iterator(): Iterator
    {
        throw new UnsupportedOperationException();
    }
}