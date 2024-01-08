<?php
/**
 * @noinspection PhpMissingDocCommentInspection
 * @noinspection PhpEnforceDocCommentInspection
 */

namespace jhp\testhelper;

use jhp\lang\TObject;

class TestObject extends TObject {
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

    public function equals(?TObject $obj = null): bool
    {
        return $obj instanceof TestObject &&
            $this->value === $obj->getValue();
    }


}