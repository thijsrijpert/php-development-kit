<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 *
 * This code is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License version 2 only, as
 * published by the Free Software Foundation.  This particular file is
 * designated as subject to the "Classpath" exception as provided in the
 * LICENSE file that accompanied this code.
 *
 * This code is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
 * version 2 for more details (a copy is included in the LICENSE file that
 * accompanied this code).
 *
 * You should have received a copy of the GNU General Public License version
 * 2 along with this work; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */
/* Copyright (c) 2007, 2021, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */
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