<?php

namespace jhp\testhelper;

class TestObject {
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