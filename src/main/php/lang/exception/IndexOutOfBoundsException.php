<?php

namespace jhp\lang\exception;

use RuntimeException;

/**
 * Thrown to indicate that an index of some sort (such as to an array, to a
 * string, or to a vector) is out of range.
 * <p>
 * Applications can subclass this class to indicate similar exceptions.
 */
class IndexOutOfBoundsException extends RuntimeException
{
}