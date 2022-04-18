<?php


namespace Framework\Database\QueryBuilder;

use Framework\Database\Connection\SqliteConnection;

class SqliteQueryBuilder extends QueryBuilder
{
    protected SqliteQueryBuilder $connection;

    public function __construct(SqliteConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function connection(): SqliteConnection
    {
        // TODO: Implement connection() method.
    }
}