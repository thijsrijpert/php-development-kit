<?php

namespace jhp\lang;

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