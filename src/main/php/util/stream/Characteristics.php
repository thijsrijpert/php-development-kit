<?php

namespace jhp\util\stream;

use jhp\util\collection\Set;

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
     * result container has no intrinsic order, such as a {@link Set}.)
     */
    case UNORDERED;

    /**
     * Indicates that the finisher function is the identity function and
     * can be elided.  If set, it must be the case that an unchecked cast
     * from A to R will succeed.
     */
    case IDENTITY_FINISH;
}