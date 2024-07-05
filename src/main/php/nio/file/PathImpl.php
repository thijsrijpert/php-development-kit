<?php

namespace jhp\nio\file;

use jhp\lang\TObject;
use jhp\lang\TString;

class PathImpl extends TObject implements Path
{
    public function __construct(private readonly TString $path)
    {

    }

    public function toString(): string
    {
        return $this->path->toString();
    }

}