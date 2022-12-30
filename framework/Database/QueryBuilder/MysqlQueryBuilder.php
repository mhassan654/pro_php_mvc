<?php

namespace Framework\Database\QueryBuilder;

use Framework\Database\Connection\Connection;
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
    public function connection(): MysqlConnection
    {
        // TODO: Implement connection() method.
        $config =[
            'host'=>'127.0.0.1',
            'port'=>'3306',
            'database'=>'promvc',
            'password'=>'##%Developer123',
            'username'=>'root'];
        return new MysqlConnection($config);
    }
}
