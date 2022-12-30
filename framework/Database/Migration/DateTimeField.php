<?php

namespace Framework\Database\Migration;

class DateTimeField extends Field
{
    public bool $default;

    public function default(bool $value): static
    {
        $this->default = $value;
        return $this;
    }
}
