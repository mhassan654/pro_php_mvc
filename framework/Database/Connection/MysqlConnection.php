<?php


namespace Framework\Database\Connection;


use Pdo;
use InvalidArgumentException;

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
}
