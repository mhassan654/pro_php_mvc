<?php


namespace Framework\Database\Connection;
use Pdo;
use Framework\Database\Migration\Migration;
use Framework\Database\QueryBuilder\QueryBuilder;


abstract class Connection
{
    /**
     * Get the underlying Pdo instance for this connection
     */
    abstract public function pdo(): Pdo;

    /**
     * Start a new query on this connection
     */
    abstract public function query(): QueryBuilder;

    /**
     * Start a new migration to add a table on this connection
     */
    abstract public function createTable(string $table): Migration;

}