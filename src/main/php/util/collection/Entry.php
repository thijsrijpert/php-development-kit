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

use jhp\lang\IObject;

/**
 * A map entry (key-value pair). The Entry may be unmodifiable, or the
 * value may be modifiable if the optional {@code setValue} method is
 * implemented. The Entry may be independent of any map, or it may represent
 * an entry of the entry-set view of a map.
 * <p>
 * Instances of the {@code Map.Entry} interface may be obtained by iterating
 * the entry-set view of a map. These instances maintain a connection to the
 * original, backing map. This connection to the backing map is valid
 * <i>only</i> for the duration of iteration over the entry-set view.
 * During iteration of the entry-set view, if supported by the backing map,
 * a change to a {@code Map.Entry}'s value via the
 * {@link Map.Entry#setValue setValue} method will be visible in the backing map.
 * The behavior of such a {@code Map.Entry} instance is undefined outside of
 * iteration of the map's entry-set view. It is also undefined if the backing
 * map has been modified after the {@code Map.Entry} was returned by the
 * iterator, except through the {@code Map.Entry.setValue} method. In particular,
 * a change to the value of a mapping in the backing map might or might not be
 * visible in the corresponding {@code Map.Entry} element of the entry-set view.
 *
 * @apiNote
 * It is possible to create a {@code Map.Entry} instance that is disconnected
 * from a backing map by using the {@link Map.Entry#copyOf copyOf} method. For example,
 * the following creates a snapshot of a map's entries that is guaranteed not to
 * change even if the original map is modified:
 * <pre> {@code
 * var entries = map.entrySet().stream().map(Map.Entry::copyOf).toList()
 * }</pre>
 *
 * @see IMap::entrySet()
 */
interface Entry {

    /**
     * Returns the key corresponding to this entry.
     *
     * @return IObject the key corresponding to this entry
     */
    function getKey(): IObject;

    /**
     * Returns the value corresponding to this entry.  If the mapping
     * has been removed from the backing map (by the iterator's
     * {@code remove} operation), the results of this call are undefined.
     *
     * @return IObject the value corresponding to this entry
     */
    function getValue(): IObject;

    /**
     * Replaces the value corresponding to this entry with the specified
     * value (optional operation).  (Writes through to the map.)  The
     * behavior of this call is undefined if the mapping has already been
     * removed from the map (by the iterator's {@code remove} operation).
     *
     * @param IObject $value new value to be stored in this entry
     * @return IObject old value corresponding to the entry
     */
    function setValue(IObject $value): IObject;
}