<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 */
/*
 * Copyright (c) 2012, 2020, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 *
 * This code is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License version 2 only, as
 * published by the Free Software Foundation.  Oracle designates this
 * particular file as subject to the "Classpath" exception as provided
 * by Oracle in the LICENSE file that accompanied this code.
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
 *
 * Please contact Oracle, 500 Oracle Parkway, Redwood Shores, CA 94065 USA
 * or visit www.oracle.com if you need additional information or have any
 * questions.
 */
namespace jhp\util;

use Exception;
use jhp\lang\exception\NullPointerException;
use jhp\lang\IObject;
use jhp\util\function\Consumer;
use jhp\util\function\GFunction;
use jhp\util\function\Predicate;
use jhp\util\function\Runnable;
use jhp\util\function\Supplier;
use jhp\util\stream\Stream;

class Optional
{
    /**
     * @param IObject|null $value the value that should be stored in this optional
     */
    private function __construct(private readonly ?IObject $value = null) {}


    /**
     * Returns an empty Optional instance.  No value is present for this
     * Optional.
     *
     * @apiNote
     * Though it may be tempting to do so, avoid testing if an object is empty
     * by comparing with == or != against instances returned by
     * Optional.empty().  There is no guarantee that it is a singleton.
     * Instead, use {@link #isEmpty()} or {@link #isPresent()}.
     *
     * @return Optional an empty Optional
     */
    public static function empty(): Optional {
       return new Optional();
    }

    /**
     * Returns an Optional describing the given non-null
     * value.
     *
     * @param IObject $value the object to store
     *
     * @return Optional Optional with the value present
     */
    public static function of(IObject $value): Optional {
        return new Optional($value);
    }

    /**
     * Returns an Optional describing the given value, if
     * non-null, otherwise returns an empty Optional.
     *
     * @param IObject|null $value The value to set, null if no value is supplied
     *
     * @return Optional Optional with a present value if the specified value
     *         is non-null, otherwise an empty Optional
     */
    public static function ofNullable(?IObject $value = null): Optional {
        return new Optional($value);
    }

    /**
     * If a value is present, returns the value, otherwise throws
     * NoSuchElementException.
     *
     * @apiNote
     * The preferred alternative to this method is {@link #orElseThrow()}.
     *
     * @return IObject the non-null value described by this Optional
     * @throws NoSuchElementException if no value is present
     */
    public function get(): IObject {
        if ($this->value == null) {
            throw new NoSuchElementException("No value present");
        }
        return $this->value;
    }

    /**
     * If a value is present, returns true, otherwise false.
     *
     * @return bool true if a value is present, otherwise false
     */
    public function isPresent(): bool {
        return $this->value != null;
    }

    /**
     * If a value is  not present, returns true, otherwise
     * false.
     *
     * @return bool true if a value is not present, otherwise false
     */
    public function isEmpty(): bool {
        return $this->value == null;
    }

    /**
     * If a value is present, performs the given action with the value,
     * otherwise does nothing.
     *
     * @param Consumer $action the action to execute with the value
     */
    public function ifPresent(Consumer $action): void {
        if ($this->value != null) {
            $action->accept($this->value);
        }
    }

    /**
     * If a value is present, performs the given action with the value,
     * otherwise performs the given empty-based action.
     *
     * @param Consumer $action the action to be performed, if a value is present
     * @param Runnable $emptyAction the empty-based action to be performed, if no value is
     *        present
     */
    public function ifPresentOrElse(Consumer $action, Runnable $emptyAction): void {
        if ($this->value != null) {
            $action->accept($this->value);
        } else {
            $emptyAction->run();
        }
    }

    /**
     * If a value is present, and the value matches the given predicate,
     * returns an Optional describing the value, otherwise returns an
     * empty Optional.
     *
     * @param Predicate $predicate
     *
     * @return Optional Optional describing the value of this
     *         Optional, if a value is present and the value matches the
     *         given predicate, otherwise an empty Optional
     */
    public function filter(Predicate $predicate): Optional {
        if (!$this->isPresent()) {
            return $this;
        }

        return $predicate->test($this->value) ? $this : Optional::empty();
    }

    /**
     * If a value is present, returns an Optional describing (as if by
     * {@link #ofNullable}) the result of applying the given mapping function to
     * the value, otherwise returns an empty Optional.
     *
     * <p>If the mapping function returns a null result then this method
     * returns an empty Optional.
     *
     * @apiNote
     * This method supports post-processing on Optional values, without
     * the need to explicitly check for a return status.  For example, the
     * following code traverses a stream of URIs, selects one that has not
     * yet been processed, and creates a path from that URI, returning
     * an Optional<Path>:
     *
     * <pre>{@code
     *     Optional<Path> p =
     *         uris.stream().filter(uri -> !isProcessedYet(uri))
     *                       .findFirst()
     *                       .map(Paths::get);
     * }</pre>
     *
     * Here, findFirst returns an Optional<URI>, and then
     * map returns an Optional<Path> for the desired
     * URI if one exists.
     *
     * @param GFunction $mapper the mapping function to apply to a value, if present
     * @param <U> The type of the value returned from the mapping function
     * @return an Optional describing the result of applying a mapping
     *         function to the value of this Optional, if a value is
     *         present, otherwise an empty Optional
     * @throws NullPointerException if the mapping function is null
     */
    public function map(GFunction $mapper) {
        if (!$this->isPresent()) {
            return Optional::empty();
        } else {
            return Optional::ofNullable($mapper->apply($this->value));
        }
    }

    /**
     * If a value is present, returns the result of applying the given
     * Optional-bearing mapping function to the value, otherwise returns
     * an empty Optional.
     *
     * <p>This method is similar to {@link #map(Function)}, but the mapping
     * function is one whose result is already an Optional, and if
     * invoked, flatMap does not wrap it within an additional
     * Optional.
     *
     * @param <U> The type of value of the Optional returned by the
     *            mapping function
     * @param mapper the mapping function to apply to a value, if present
     * @return the result of applying an Optional-bearing mapping
     *         function to the value of this Optional, if a value is
     *         present, otherwise an empty Optional
     * @throws NullPointerException if the mapping function is null or
     *         returns a null result
     */
    public function flatMap(GFunction $mapper): Optional {
        if (!$this->isPresent()) {
            return Optional::empty();
        }
        return Optional::of($mapper->apply($this->value));
    }

    /**
     * If a value is present, returns an Optional describing the value,
     * otherwise returns an Optional produced by the supplying function.
     *
     * @param supplier the supplying function that produces an Optional
     *        to be returned
     * @return returns an Optional describing the value of this
     *         Optional, if a value is present, otherwise an
     *         Optional produced by the supplying function.
     * @since 9
     */
    public function or(Supplier $supplier): Optional {
        if ($this->isPresent()) {
            return $this;
        }
        return Optional::of($supplier->get());
    }

    /**
     * If a value is present, returns a sequential {@link Stream} containing
     * only that value, otherwise returns an empty Stream.
     *
     * @apiNote
     * This method can be used to transform a Stream of optional
     * elements to a Stream of present value elements:
     * <pre>{@code
     *     Stream<Optional<T>> os = ..
     *     Stream<T> s = os.flatMap(Optional::stream)
     * }</pre>
     *
     * @return the optional value as a Stream
     * @since 9
     */
    public function stream(): Stream {
        throw new NullPointerException();
    }

    /**
     * If a value is present, returns the value, otherwise returns
     * other.
     *
     * @param other the value to be returned, if no value is present.
     *        May be null.
     * @return the value, if present, otherwise other
     */
    public function orElse(object $other): object {
        return $this->value != null ? $this->value : $other;
    }

    /**
     * If a value is present, returns the value, otherwise returns the result
     * produced by the supplying function.
     *
     * @param supplier the supplying function that produces a value to be returned
     * @return the value, if present, otherwise the result produced by the
     *         supplying function
     * @throws NullPointerException if no value is present and the supplying
     *         function is null
     */
    public function orElseGet(Supplier $supplier): object {
        return $this->value != null ? $this->value : $supplier->get();
    }

    /**
     * If a value is present, returns the value, otherwise throws
     * NoSuchElementException.
     *
     * @return the non-null value described by this Optional
     * @throws NoSuchElementException if no value is present
     * @throws Exception
     * @since 10
     */
    public function orElseThrow(?Supplier $exceptionSupplier = null): object {
        if ($this->value == null && $exceptionSupplier === null) {
            throw new NoSuchElementException("No value present");
        } else if($this->value == null) {
            $e = $exceptionSupplier->get();
            throw $e instanceof Exception
                ? $e
                : new NoSuchElementException("No value present, actual error supplier supplies invalid type");
        }
        return $this->value;
    }

    /**
     * Indicates whether some other object is "equal to" this Optional.
     * The other object is considered equal if:
     * <ul>
     * <li>it is also an Optional and;
     * <li>both instances have no value present or;
     * <li>the present values are "equal to" each other via equals().
     * </ul>
     *
     * @param obj an object to be tested for equality
     * @return true if the other object is "equal to" this object
     *         otherwise false
     */
    public function equals(object $obj): bool {
        if ($this->value == $obj->value) {
            return true;
        }
        return false;
    }
}