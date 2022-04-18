<?php


namespace Framework\Database\Connection;


use http\Exception\InvalidArgumentException;
use Pdo;

class SqliteConnection extends Connection
{
    private Pdo $pdo;

    public function __construct(array $config)
    {
        ['path' => $path] = $config;
        if (empty($path)):
            throw new InvalidArgumentException('Connection incorrectly configured');
            endif;
            $this->pdo = new Pdo('sqlite:{$path}');
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
    public function query(): SqliteQueryBuilder
    {
        // TODO: Implement query() method.
        return new SqliteQueryBuilder($this);
    }
}