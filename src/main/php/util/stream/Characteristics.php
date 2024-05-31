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
 * Copyright (c) 2012, 2019, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */
namespace jhp\util\stream;

use jhp\util\collection\ISet;

/**
 * Characteristics indicating properties of a Collector, which can
 * be used to optimize reduction implementations.
 */
enum Characteristics {
    /**
     * Indicates that this collector is <em>concurrent</em>, meaning that
     * the result container can support the accumulator function being
     * called concurrently with the same result container from multiple
     * threads.
     *
     * <p>If a CONCURRENT collector is not also UNORDERED,
     * then it should only be evaluated concurrently if applied to an
     * unordered data source.
     */
    case CONCURRENT;

    /**
     * Indicates that the collection operation does not commit to preserving
     * the encounter order of input elements.  (This might be true if the
     * result container has no intrinsic order, such as a {@link ISet}.)
     */
    case UNORDERED;

    /**
     * Indicates that the finisher function is the identity function and
     * can be elided.  If set, it must be the case that an unchecked cast
     * from A to R will succeed.
     */
    case IDENTITY_FINISH;
}