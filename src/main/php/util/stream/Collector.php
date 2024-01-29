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

use jhp\util\collection\Set;
use jhp\util\function\BiConsumer;
use jhp\util\function\BinaryOperator;
use jhp\util\function\GFunction;
use jhp\util\function\Supplier;

abstract class Collector {

    protected function __construct(protected readonly Supplier $supplier,
                                   protected readonly BiConsumer $accumulator,
                                   protected readonly BinaryOperator $combiner,
                                   protected readonly GFunction $finisher,
                                   protected readonly Set $characteristics){ }

    /**
     * A function that creates and returns a new mutable result container.
     *
     * @return Supplier a function which returns a new, mutable result container
     */
    abstract function supplier(): Supplier;

    /**
     * A function that folds a value into a mutable result container.
     *
     * @return BiConsumer a function which folds a value into a mutable result container
     */
    abstract function accumulator(): BiConsumer;

    /**
     * A function that accepts two partial results and merges them.  The
     * combiner function may fold state from one argument into the other and
     * return that, or may return a new result container.
     *
     * @return BinaryOperator a function which combines two partial results into a combined
     * result
     */
     abstract function combiner(): BinaryOperator;

    /**
     * Perform the final transformation from the intermediate accumulation type
     * A to the final result type R.
     *
     * <p>If the characteristic IDENTITY_FINISH is
     * set, this function may be presumed to be an identity transform with an
     * unchecked cast from A to R.
     *
     * @return GFunction a function which transforms the intermediate result to the final
     * result
     */
    abstract function finisher(): GFunction;

    /**
     * Returns a Set of Collector.Characteristics indicating
     * the characteristics of this Collector.  This set should be immutable.
     *
     * @return Set an immutable set of collector characteristics
     */
    abstract function characteristics(): Set;

    public static function of(
        Supplier $supplier,
        BiConsumer $accumulator,
        BinaryOperator $combiner,
        ?GFunction $finisher = null,
        Characteristics ...$characteristics): Collector {

        if ($finisher == null) {
            // Improve this to be more compliant with JDK Spec
            $finisher = GFunction::of(fn($i) => $i);
        }

        // Add support for characteristics
        return new class($supplier, $accumulator, $combiner, $finisher, new Set()) extends Collector {

            function supplier(): Supplier
            {
                return $this->supplier;
            }

            function accumulator(): BiConsumer
            {
                return $this->accumulator;
            }

            function combiner(): BinaryOperator
            {
                return $this->combiner;
            }

            function finisher(): GFunction
            {
                return $this->finisher;
            }

            function characteristics(): Set
            {
                return $this->characteristics;
            }
        };
    }
}