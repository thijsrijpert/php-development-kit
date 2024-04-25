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
 * Copyright (c) 1997, 2021, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */
namespace jhp\util\collection;

use jhp\lang\IObject;

class THashMapEntry implements Entry
{

    public function __construct(private readonly IObject $key,
                                private IObject $value
    ) {}

    function getKey(): IObject
    {
        return $this->key;
    }

    function getValue(): IObject
    {
        return $this->value;
    }

    function setValue(IObject $value): IObject
    {
        $oldValue = $this->value;
        $this->value = $value;
        return $oldValue;
    }

    public static function of(IObject $key, IObject $value): Entry {
        return new THashMapEntry($key, $value);
    }
}