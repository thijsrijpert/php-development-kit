<?php

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