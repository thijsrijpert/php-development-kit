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
/**
 * @noinspection PhpMissingDocCommentInspection
 * @noinspection PhpEnforceDocCommentInspection
 */

namespace jhp\testhelper;

use jhp\lang\IObject;
use jhp\lang\TObject;

class HashableTestObject extends TObject {
    private int $value = 6;
    private bool $setterInvoked = false;

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->setterInvoked = true;
        $this->value = $value;
        return $this;
    }

    public function isSetterInvoked(): bool
    {
        return $this->setterInvoked;
    }

    public function equals(?IObject $obj = null): bool
    {
        return $obj->getClass()->getName() === $this->getClass()->getName() &&
            $this->value === $obj->getValue();
    }

    public function hashCode(): int
    {
        return $this->value / 2;
    }


}