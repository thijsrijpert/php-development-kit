<?php

namespace jhp\util\function\internal;

use TypeError;

class TypeErrorHelper
{
    private function __construct() {

    }

    public static function convertToFunctionalTypeError(TypeError $e): TypeError {
        $message = $e->getMessage();
        $message = preg_replace("/\((.*?)\)/", "", $message);
        $message = str_replace("@anonymous", "()", $message);
        $message = preg_replace('/\s+/', ' ', $message);
        return new TypeError($message, 0, $e);
    }
}