<?php

namespace jhp\nio\file;

use jhp\lang\TObject;
use jhp\lang\TString;

class Paths extends TObject
{
    private function __construct()
    {

    }
    public static function get(TString $first): Path {
        return new PathImpl($first);
    }
}