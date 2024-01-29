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