<?php

namespace jhp\io;

use Exception;
use jhp\lang\IObject;
use jhp\lang\ObjectTrait;

class IOException extends Exception implements IObject
{
    use ObjectTrait;
}