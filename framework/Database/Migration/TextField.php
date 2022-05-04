<?php

namespace Framework\Database\Migration;

class TextField extends Field
{
    public bool $default;

    public function default(bool $value): static
    {
        $this->default = $value;
        return $this;
    }
}
