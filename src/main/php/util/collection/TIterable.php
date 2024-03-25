<?php

namespace jhp\util\collection;

use jhp\lang\exception\UnsupportedOperationException;
use jhp\lang\IIterable;
use jhp\lang\TObject;
use jhp\util\Spliterator;
use Traversable;

abstract class TIterable extends TObject implements IIterable
{
    /**
     * @return Traversable An instance of an object implementing <b>Iterator</b> or <b>Traversable</b>
     */
    public function getIterator(): Traversable
    {
        return $this->iterator();
    }

    public function spliterator(): Spliterator
    {
        throw new UnsupportedOperationException();
    }
}