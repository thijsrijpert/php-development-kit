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

namespace jhp\lang\internal;

use jhp\lang\EnumTrait;
use jhp\lang\IEnum;

enum GType implements IEnum
{

    use EnumTrait;

    case BOOLEAN;
    case INTEGER;
    case FLOAT;
    case STRING;
    case ARRAY;
    case OBJECT;
    case RESOURCE;
    case RESOURCE_CLOSED;
    case NULL;
    case UNKNOWN;

    public function isBoolean(): bool {
        return $this === GType::BOOLEAN;
    }

    public function isInteger(): bool {
        return $this === GType::INTEGER;
    }

    public function isFloat(): bool {
        return $this === GType::FLOAT;
    }

    public function isString(): bool {
        return $this === GType::STRING;
    }

    public function isArray(): bool {
        return $this === GType::ARRAY;
    }

    public function isObject(): bool {
        return $this === GType::OBJECT;
    }

    public function isResource(): bool {
        return $this === GType::RESOURCE;
    }

    public function isClosedResource(): bool {
        return $this === GType::RESOURCE_CLOSED;
    }

    public function isNull(): bool {
        return $this === GType::NULL;
    }

    public function isUnknown(): bool {
        return $this === GType::UNKNOWN;
    }

    public static function from(string $type): GType {
        return match(strtolower($type)) {
            "boolean", "bool" => GType::BOOLEAN,
            "integer", "int" => GType::INTEGER,
            "double", "float" => GType::FLOAT,
            "string" => GType::STRING,
            "array" => GType::ARRAY,
            "object" => GType::OBJECT,
            "resource" => GType::RESOURCE,
            "resource (closed)" => GType::RESOURCE_CLOSED,
            "null", "void" => GType::NULL,
            "unknown type" => GType::UNKNOWN,
        };
    }

    public static function of(mixed $value): GType {
        return GType::from(gettype($value));
    }
}