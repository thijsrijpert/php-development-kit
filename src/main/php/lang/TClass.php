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
 * Copyright (c) 1994, 2021, Oracle and/or its affiliates. All rights reserved.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 */

namespace jhp\lang;

use Defuse\Crypto\Exception\IOException;
use jhp\lang\exception\IllegalArgumentException;
use jhp\util\collection\ArrayList;
use jhp\util\collection\IList;

/**
 * A class representing the properties of a class specification
 */
final class TClass extends TObject
{

    /**
     * @param string $className The name of the class
     */
    private function __construct(private readonly string $className) { }

    /**
     * Retrieve a list of interfaces implemented by this class
     * @return IList A list of interfaces implemented by this class
     */
    public function getInterfaces(): IList {
        $list = new ArrayList(TClass::from(TClass::class));
        foreach (class_implements($this->className) as $value) {
            $list->add(TClass::of($value));
        }
        return $list;
    }

    /**
     * Gets the full name of the class
     * @return string The name of the class including the namespace
     */
    public function getName(): string {
        return $this->className;
    }

    /**
     * Tests if the supplied object could be cast to this class
     * @param object $obj The object to test
     *
     * @return bool true if the object is of this class
     */
    public function isInstance(IObject $obj): bool {
        return is_a($obj, $this->getName());
    }

    /**
     * Determines if the class or interface represented by this
     * Class object is either the same as, or is a superclass or
     * superinterface of, the class or interface represented by the specified
     * class parameter. It returns true if so;
     * otherwise it returns false. If this class
     * object represents a primitive type, this method returns
     * true if the specified Class parameter is
     * exactly this Class object; otherwise it returns
     * false.
     *
     * @param     TClass $clazz the {@code Class} object to be checked
     * @return    bool the {@code boolean} value indicating whether objects of the
     *            type {@code cls} can be assigned to objects of this class
     */
    public function isAssignableFrom(TClass $clazz): bool {
        return is_a($clazz->getName(), $this->getName(), true);
    }

    /**
     *
     * @param   ?TObject $obj the reference object with which to compare.
     *
     * @return  bool true if this object is the same as the obj argument; false otherwise.
     * @api
     * It is generally necessary to override the {@link TObject::hashCode}
     * method whenever this method is overridden, to maintain the
     * general contract for the hashCode method, which states
     * that equal objects must have equal hash codes.
     *
     * @see     TObject::hashCode()
     * @see     THashMap
     */
    public function equals(?TObject $obj = null): bool {
        if ($obj == null) {
            return false;
        }
        return $obj instanceof TClass && strtolower($this->getName()) === strtolower($obj->getName());
    }

    /**
     * Create an instance of TClass representing a single class
     * @param string $type The type to create a class from
     *
     * @return TClass The class instance representing the supplied type
     */
    public static function from(string $type): TClass {
        if (!class_exists($type)) {
            throw new IllegalArgumentException("Class " . $type . " does not exist");
        }
        return new TClass($type);
    }

    /**
     * @param IObject $value The object that we need to know the class of
     *
     * @return TClass The static type of the object
     */
    public static function of(IObject $value): TClass
    {
        return new TClass(get_class($value));
    }
}