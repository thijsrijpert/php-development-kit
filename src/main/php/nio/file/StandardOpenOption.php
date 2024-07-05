<?php

namespace jhp\nio\file;

use jhp\lang\EnumTrait;
use jhp\lang\IEnum;

enum StandardOpenOption implements OpenOption, IEnum
{
    use EnumTrait;

    /**
     * Bytes will be written to the end of the file rather than the beginning.
     */
    case APPEND;

    /**
     * If the file already exists, then its length is truncated to 0.
     */
    case TRUNCATE_EXISTING;

    /**
     * Create a new file if it does not exist.
     * This option is ignored if the CREATE_NEW option is also set.
     * The check for the existence of the file and the creation of the file
     * if it does not exist is atomic with respect to other file system
     * operations.
     */
    case CREATE;

    /**
     * Create a new file, failing if the file already exists.
     * The check for the existence of the file and the creation of the file
     * if it does not exist is atomic with respect to other file system
     * operations.
     */
    case CREATE_NEW;
}
