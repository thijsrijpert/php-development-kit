<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 */
namespace jhp\testhelper;

use jhp\lang\TObject;

class NotTestObject extends TObject {
    private string $value = "DefaultValue";
    private bool $setterInvoked = false;

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->setterInvoked = true;
        $this->value = $value;
        return $this;
    }

    public function isSetterInvoked(): bool
    {
        return $this->setterInvoked;
    }


}