<?php

namespace jhp\lang\exception;

use jhp\lang\TInteger;

/**
 * Thrown to indicate that the application has attempted to convert
 * a string to one of the numeric types, but that the string does not
 * have the appropriate format.
 *
 * @see     TInteger::parseInt()
 */
class NumberFormatException extends IllegalArgumentException
{
    /**
     * Factory method for making a <code>NumberFormatException</code>
     * given the specified input which caused the error.
     *
     * @param string $s the input causing the error
     */
    static function forInputString(String $s): NumberFormatException {
        return new NumberFormatException("For input string: \"" . $s . "\"");
    }
}