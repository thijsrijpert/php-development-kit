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
namespace jhp\lang\functional;

use jhp\lang\IObject;
use jhp\lang\TClass;
use jhp\lang\TInteger;
use jhp\lang\TString;
use jhp\util\collection\IList;
use jhp\util\collection\Lists;

if (!function_exists('jhp\lang\functional\s')) {
    function s(string $string): TString
    {
        return TString::valueOf($string);
    }
}

if (!function_exists('jhp\lang\functional\c')) {
    function c(string $clazz): TClass
    {
        return TClass::from($clazz);
    }
}

if (!function_exists('jhp\lang\functional\i')) {
    function i(int $int): TInteger
    {
        return TInteger::valueOf($int);
    }
}

if (!function_exists('jhp\lang\functional\l')) {
    function l(IObject ...$objects): IList
    {
        return Lists::ofMutable(...$objects);
    }
}
