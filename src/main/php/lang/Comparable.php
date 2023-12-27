<?php

namespace jhp\lang;

interface Comparable
{
    public function compareTo(object $o): int;
}