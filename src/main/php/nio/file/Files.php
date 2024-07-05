<?php

namespace jhp\nio\file;

use jhp\io\FileAlreadyExistsException;
use jhp\io\IOException;
use jhp\lang\IIterable;
use jhp\lang\IObject;
use jhp\lang\TString;
use TypeError;

class Files
{
    /**
     * @throws IOException
     * @throws FileAlreadyExistsException
     */
    public static function write(Path $path, IIterable $lines, OpenOption ...$openOptions) {
        $toWrite = "";

        foreach($lines as $line) {
            if (!($line instanceof TString)) {
                throw new TypeError("line is not instance of TString");
            }
            $toWrite .= $line->toString() . PHP_EOL;
        }

        $flags = 0;

        foreach($openOptions as $openOption) {
            $flags = $openOption->equals(StandardOpenOption::APPEND) ? FILE_APPEND : $flags;
            if ($openOption->equals(StandardOpenOption::CREATE_NEW) && file_exists($path->toString())) {
                throw new FileAlreadyExistsException($path->toString());
            }
        }

        if (file_put_contents($path->toString(), $toWrite, $flags) === false) {
            throw new IOException("Failed to write to path:" . $path->toString());
        }

        return $path;
    }
}