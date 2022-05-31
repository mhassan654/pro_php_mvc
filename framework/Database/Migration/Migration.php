<?php


namespace Framework\Database\Migration;


use Framework\Database\Migration\BoolField;
use Framework\Database\Migration\FloatField;
use Framework\Database\Connection\Connection;
use Framework\Database\Migration\DateTimeField;

abstract class Migration
{
    protected array $fields =[];

    public function bool(string $name): BoolField
    {
        $field = $this->fields[] = new BoolField($name);
        return $field;
    }

    public function dateTime(string $name): DateTimeField
    {
        $field = $this->fields[] = new DateTimeField($name);
        return $field;
    }

    public function float(string $name): FloatField
    {
        $field = $this->fields[] = new FloatField($name);
        return $field;
    }

    public function id(string $name): IdField
    {
        $field = $this->fields[] = new IdField($name);
        return $field;
    }

    public function int(string $name): IntField
    {
        $field = $this->fields[] = new IntField($name);
        return $field;
    }

    public function string(string $name): StringField
    {
        $field = $this->fields[] = new StringField($name);
        return $field;
    }

    public function text(string $name): TextField
    {
        $field = $this->fields[] = new TextField($name);
        return $field;
    }

    abstract public function connection(): Connection;
    abstract public function execute(): void;

}