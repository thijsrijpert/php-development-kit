<?php

namespace jhp\lang;

class Clazz
{

    public function __construct(private readonly string $className) { }

    public function getName() {
        return $this->className;
    }

    public static function from(string $type): Clazz {
        return new Clazz(strtolower($type));
    }

    public static function of(mixed $value): Clazz {
        if ($value === null) {
            throw new NullPointerException("Cannot create class reference, object is null");
        }
        return new Clazz(strtolower(get_class($value)));
    }

}