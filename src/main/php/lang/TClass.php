<?php

namespace jhp\lang;

use jhp\lang\exception\IllegalArgumentException;
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

    public function isInstance(Object $obj): bool {
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