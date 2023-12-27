<?php

namespace jhp\lang;

use jhp\util\function\internal\NullPointerException;

final readonly class Clazz
{

    public function __construct(private string $className) { }

    public function getName(): string {
        return $this->className;
    }

    public function equals(Clazz $other): bool {
        return $this->getName() === $other->getName();
    }

    public static function from(string $type): Clazz {
        return new Clazz(strtolower($type));
    }

    public static function of(?object $value): Clazz
    {
        if ($value === null) {
            throw new NullPointerException("Cannot create class reference, object is null");
        }
        return new Clazz(strtolower(get_class($value)));
    }

}