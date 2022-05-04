<?php

namespace Framework\Database\Migration;

class FloatField extends Field
{
    public bool $default;

    public function default(bool $value): static
    {
        $this->default = $value;
        return $this;
    }
}
