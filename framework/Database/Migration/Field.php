<?php


namespace Framework\Database\Migration;


abstract class Field
{
    public string $name;
    public bool $nullable = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function nullable(): static
    {
        $this->nullable = true;
        return $this;
    }

}