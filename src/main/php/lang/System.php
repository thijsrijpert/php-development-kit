<?php

namespace jhp\lang;

class System extends TObject
{

    public static function identityHashCode(TObject $object): int {
        return spl_object_id($object);
    }
}