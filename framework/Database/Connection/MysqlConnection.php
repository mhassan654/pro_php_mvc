<?php


namespace Framework\Database\Connection;


use Pdo;
use InvalidArgumentException;
use Framework\Database\Migration\Migration;
use Framework\Database\Migration\MysqlMigration;
use Framework\Database\QueryBuilder\MysqlQueryBuilder;

class MysqlConnection extends Connection
{
    private Pdo $pdo;

    public function __construct(array $config)
    {
        [
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
        ] = $config;

        if(empty($host) || empty($database) || empty($username)):
            throw new InvalidArgumentException('Connection incorrectly configured');
        endif;

        $this->pdo = new Pdo("mysql:host={$host}:port={$port};dbname={$database}", $username,$password);
        
    }

    /**
     * @inheritDoc
     */
    public function pdo(): Pdo
    {
        // TODO: Implement pdo() method.
        return $this->pdo;

    }

    /**
     * @inheritDoc
     */
    public function query(): MysqlQueryBuilder
    {
        // TODO: Implement query() method.
        return new MysqlQueryBuilder($this);
    }

    /**
     * @param string $table
     * @return Migration
     */
    public function createTable(string $table): MysqlMigration
    {
        // TODO: Implement createTable() method.
        return new MysqlMigration($this, $table, 'create');
    }
}
