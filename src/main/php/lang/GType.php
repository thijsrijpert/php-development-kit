<?php

namespace jhp\lang;

enum GType
{
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