<?php


namespace Framework\Database\QueryBuilder;

use Framework\Database\Connection\MysqlConnection;

class MysqlQueryBuilder extends QueryBuilder
{
    protected MysqlConnection $connection;

    public function __construct(MysqlConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
//    public function connection(): MysqlConnection
//    {
//        // TODO: Implement connection() method.
//        return new MysqlConnection($this);
//    }
}