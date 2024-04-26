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

class THashMap extends TIterable implements IMap
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

    public function __construct(TClass $keyType, TClass $valueType, ?IMap $collection = null) {
        $this->keyType = $keyType;
        $this->valueType = $valueType;
        if ($collection !== null) {
            if ($keyType->isAssignableFrom($collection->getKeyType())) {
                throw new IllegalStateException("Trying to copy a  map with keyType:"
                    . $collection->getValueType()->getName()
                    . "into a hashmap with keyType: "
                    . $valueType->getName());
            }
            if ($valueType->isAssignableFrom($collection->getValueType())) {
                throw new IllegalStateException("Trying to copy a  map with valueType:"
                    . $collection->getValueType()->getName()
                    . "into a hashmap with valueType: "
                    . $valueType->getName());
            }
            $collection->forEach(BiConsumer::of(fn(IObject $o1, IObject $o2) => $this->put($o1, $o2)));
        }
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
        $this->checkObjectType($this->keyType, $key);
        $bucket = $this->hashmap[$this->hash($key)];

        if ($bucket === null) {
            return null;
        }

        foreach ($bucket as $entry) {
            if (!($entry instanceof Entry)) {
                throw new RuntimeException("Invalid state of the hashmap");
            }

            if ($entry->getKey()->equals($key)) {
                return $entry->getValue();
            }
        }

        return null;
    }

    public function size(): int
    {
        return count($this->hashmap, COUNT_RECURSIVE) - count($this->hashmap);
    }

    public function isEmpty(): bool
    {
        return $this->size() === 0;
    }

    public function containsKey(IObject $key): bool
    {
        return $this->get($key) !== null;
    }

    public function containsValue(IObject $value): bool
    {
        $this->checkObjectType($this->valueType, $value);
        foreach($this->hashmap as $bucket) {
            foreach ($bucket as $entry) {
                if (!($entry instanceof Entry)) {
                    throw new IllegalStateException("Invalid state of the hashmap");
                }
                if ($entry->getValue()->equals($value)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function put(IObject $key, IObject $value): ?IObject
    {
        $this->checkObjectType($this->keyType, $key);
        $this->checkObjectType($this->valueType, $value);

        $hashcode = $this->hash($key);
        $bucket = $this->hashmap[$hashcode] ?? null;

        if ($bucket === null) {
            $entry = new THashMapEntry($key, $value);
            $this->hashmap[$hashcode] = [$entry];
            return null;
        }

        foreach ($bucket as $entry) {
            if (!($entry instanceof Entry)) {
                throw new IllegalStateException("Invalid state of the hashmap");
            }
            if ($entry->getKey()->equals($key)) {
                $oldValue = $entry->getValue();
                $entry->setValue($value);
                return $oldValue;
            }
        }

        $entry = new THashMapEntry($key, $value);
        $bucket[] = $entry;
        $this->hashmap[$hashcode] = $bucket;
        return null;
    }

    public function putAll(IMap $m): void
    {
        if (!$this->getKeyType()->isAssignableFrom($m->getKeyType())) {
            throw new IllegalArgumentException("Trying to add items from map with keyType " . $m->getKeyType()->getName() .
                " to map with keyType " . $this->getKeyType()->getName());
        }

        if (!$this->getValueType()->isAssignableFrom($m->getValueType())) {
            throw new IllegalArgumentException("Trying to add items from map with valueType " . $m->getValueType()->getName() .
                " to map with valueType " . $this->getValueType()->getName());
        }

        // $this will be the
        $map = $this;

        $m->foreach(BiConsumer::of(function (IObject $key, IObject $value) use ($map) { $map->put($key, $value); }));
    }

    public function clear(): void
    {
        $this->hashmap = [];
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
        $this->checkObjectType($this->keyType, $key);
        $this->checkObjectType($this->valueType, $defaultValue);
        $value = $this->get($key);
        if ($value === null) {
            return $defaultValue;
        }

        return $value;
    }

    public function forEach(BiConsumer $action): void
    {
        foreach ($this->hashmap as $bucket) {
            foreach ($bucket as $entry) {
                if (!($entry instanceof Entry)) {
                    throw new IllegalStateException("Invalid state of the hashmap");
                }
                $action->accept($entry->getKey(), $entry->getValue());
            }
        }
    }

    public function replaceAll(BiFunction $function): void
    {
        foreach ($this->hashmap as $bucket) {
            foreach ($bucket as $entry) {
                if (!($entry instanceof Entry)) {
                    throw new IllegalStateException("Invalid state of the hashmap");
                }
                $entry->setValue($function->apply($entry->getKey(), $entry->getValue()));
            }
        }
    }

    public function putIfAbsent(IObject $key, IObject $value): IObject
    {
        throw new UnsupportedOperationException();
    }

    public function remove(IObject $key, ?IObject $value = null): ?IObject
    {
        $this->checkObjectType($this->keyType, $key);
        if ($value !== null) {
            $this->checkObjectType($this->valueType, $value);
        }

        $hashcode = $this->hash($key);
        $bucket = &$this->hashmap[$hashcode];

        if ($bucket === null) {
            return null;
        }

        foreach ($bucket as $index => $entry) {
            if (!($entry instanceof Entry)) {
                throw new IllegalStateException("Invalid state of the hashmap");
            }
            if ($entry->getKey()->equals($key) && ($value === null || $entry->getValue()->equals($value))) {
                $oldValue = $entry->getValue();
                array_splice($bucket, $index, 1);
                return $oldValue;
            }
        }

        return null;
    }

    public function replace(IObject $key, IObject $value, ?IObject $newValue = null): ?IObject
    {
        $checkValue = $newValue === null ? null : $value;
        $newValue = $newValue === null ? $value : $newValue;

        $this->checkObjectType($this->keyType, $key);
        if ($checkValue !== null) {
            $this->checkObjectType($this->valueType, $checkValue);
        }
        $this->checkObjectType($this->valueType, $newValue);


        $hashcode = $this->hash($key);
        $bucket = $this->hashmap[$hashcode];

        if ($bucket === null) {
            return null;
        }

        foreach ($bucket as $entry) {
            if (!($entry instanceof Entry)) {
                throw new IllegalStateException("Invalid state of the hashmap");
            }
            if ($entry->getKey()->equals($key) && ($checkValue === null || $entry->getValue()->equals($checkValue))) {
                $oldValue = $entry->getValue();
                $entry->setValue($newValue);
                return $oldValue;
            }
        }

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

    public function offsetExists(mixed $offset): bool
    {
        if(!GType::of($offset)->isObject()
            || !($offset instanceof IObject)) {
            throw new IllegalArgumentException("Key should be IObject");
        }

        return $this->containsKey($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if(!GType::of($offset)->isObject()
            || !($offset instanceof IObject)) {
            throw new IllegalArgumentException("Key should be IObject");
        }

        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if(!GType::of($offset)->isObject()
            || !GType::of($value)->isObject()
            || !($value instanceof IObject)
            || !($offset instanceof IObject)
        ) {
            throw new IllegalArgumentException("Key and Value should be IObject");
        }

        $this->put($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        if(!GType::of($offset)->isObject()
            || !($offset instanceof IObject)) {
            throw new IllegalArgumentException("Key should be IObject");
        }

        $this->remove($offset);
    }

    public function iterator(): Iterator
    {
        throw new UnsupportedOperationException();
    }

    public static function ofEntries(TClass $keyType, TClass $valueType, Entry ...$entries): THashMap {
        $hashmap =  new THashMap($keyType, $valueType);
        foreach ($entries as $entry) {
            $hashmap->put($entry->getKey(), $entry->getValue());
        }

        return $hashmap;
    }
}