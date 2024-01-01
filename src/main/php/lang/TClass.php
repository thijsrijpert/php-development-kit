<?php

namespace jhp\lang;

use jhp\util\collection\ArrayList;
use jhp\util\collection\IList;
use jhp\util\function\internal\NullPointerException;

final class TClass extends TObject
{

    public function __construct(private readonly string $className) { }

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

    public function equals(?TObject $obj = null): bool {
        if ($obj == null) {
            return false;
        }
        return $obj instanceof TClass && $this->getName() === $obj->getName();
    }

    public static function from(string $type): TClass {
        return new TClass(strtolower($type));
    }

    public static function of(?object $value): TClass
    {
        if ($value === null) {
            throw new NullPointerException("Cannot create class reference, object is null");
        }
        return new TClass(strtolower(get_class($value)));
    }
}