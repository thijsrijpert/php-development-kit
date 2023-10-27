<?php

namespace jhp\util\function;

use Closure;

interface FunctionalInterface
{
    function getClosure(): Closure;
}