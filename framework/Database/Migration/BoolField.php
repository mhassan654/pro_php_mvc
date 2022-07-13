<?php

namespace Framework\Database\Migration;

class BoolField extends Field
{
    public bool $default;

    public function default(bool $value): static
    {
        $this->default = $value;
        return $this;
    }
}
