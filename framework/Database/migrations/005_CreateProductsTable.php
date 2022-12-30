<?php

namespace Framework\Database\Migrations;

use Framework\Database\Connection\Connection;

class CreateProductsTable
{
    protected array $values;

    public function migrate(Connection $connection)
    {
        $table = $connection->createTable('products');
        $table->id('id');
        $table->string('name');
        $table->text('description');
        $table->execute();
    }

    // public function insert
}
