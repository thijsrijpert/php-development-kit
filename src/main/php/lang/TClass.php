<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 */
/*
 * Copyright (c) 1994, 2021, Oracle and/or its affiliates. All rights reserved.
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

namespace jhp\lang;

use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\exception\NullPointerException;
use jhp\util\collection\ArrayList;
use jhp\util\collection\IList;

final class TClass extends TObject
{

    private function __construct(private readonly string $className) { }

    public function getInterfaces(): IList {
        $list = new ArrayList(TClass::from(TClass::class));
        foreach (class_implements($this->className) as $value) {
            $list->add(TClass::of($value));
        }
        return $list;
    }

    public function getName(): string {
        return $this->className;
    }

    public function isInstance(object $obj): bool {
        return is_a($obj, $this->getName());
    }

    public function isAssignableFrom(TClass $clazz): bool {
        return is_a($clazz->getName(), $this->getName(), true);
    }

    public function equals(?TObject $obj = null): bool {
        if ($obj == null) {
            return false;
        }
        return $obj instanceof TClass && strtolower($this->getName()) === strtolower($obj->getName());
    }

    public static function from(string $type): TClass {
        if (!class_exists($type)) {
            throw new IllegalArgumentException("Class " . $type . " does not exist");
        }
        return new TClass($type);
    }

    public static function of(?object $value): TClass
    {
        if ($value === null) {
            throw new NullPointerException("Cannot create class reference, object is null");
        }
        return new TClass(get_class($value));
    }
}