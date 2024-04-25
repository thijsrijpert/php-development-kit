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
/*
 * Copyright (c) 1994, 2022, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */
namespace jhp\lang;

use Stringable;
use Symfony\Component\String\CodePointString;

class TString extends TObject implements Stringable {

    private function __construct(private readonly CodePointString $value,
                                private int $hashCode = 0,
                                private bool $hashCodeIsZero = false) { }

    public function toCodePointString(): CodePointString {
        return $this->value;
    }

    public function hashCode(): int
    {

        if ($this->value->length() === 0 || $this->hashCodeIsZero) {
            return 0;
        }

        if ($this->hashCode !== 0) {
            return $this->hashCode();
        }

        for ($i = 0; $i < $this->value->length(); $i++) {
            // Do a Java style sum instead of regular sum (with rolling negative), use bitshift instead of multiply to prevent overflow
            // From Effective Java, Second Edition
            /**
             * The value 31 was chosen because it is an odd prime.
             * If it were even and the multiplication overflowed,
             * information would be lost, as multiplication by 2 is equivalent to shifting.
             * The advantage of using a prime is less clear, but it is traditional.
             * A nice property of 31 is that the multiplication can be replaced by a shift and a subtraction for better performance:
             * 31 * i == (i << 5) - i.
             */
            $this->hashCode = TInteger::sum(TInteger::sum($this->hashCode << 5, -$this->hashCode), $this->value->codePointsAt($i)[0]);
        }

        if ($this->hashCode === 0) {
            $this->hashCodeIsZero = true;
        }

        return $this->hashCode;
    }

    public function equals(?IObject $obj = null): bool
    {
        if ($obj === null || !$obj->getClass()->equals($this->getClass())) {
            return false;
        }

        if (!($obj instanceof TString)) {
            return false;
        }

        return $obj->value->equalsTo($this->value);
    }

    public function toString(): string
    {
        return $this->value->toString();
    }

    public static function valueOf(string $string): TString {
        return new TString(new CodePointString($string));
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}