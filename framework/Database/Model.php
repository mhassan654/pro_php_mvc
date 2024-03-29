<?php

namespace Framework\Database;

use Exception;
use Framework\Database\ModelCollector;
use Framework\Database\Connection\Connection;
use Framework\Database\Connection\MysqlConnection;
use Framework\Database\Connection\SqliteConnection;

abstract class Model
{
    protected Connection $connection;
    protected string $table;
    protected array $attributes;

    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    public function getConnection(): Connection
    {
        if (isset($this->connection)){
            $factory = new Factory();

            $factory->addConnector('mysql',function ($config){
                return new MysqlConnection($config);
            });
            $factory->addConnector('sqlite',function ($config){
                return new SqliteConnection($config);
            });

            $config = require basePath(). 'config/database.php';
            $this->connection = $factory->connect($config[$config['default']]);
        }
        return $this->connection;
    }

    public function setTable(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function getTable(): string
    {
        if (!isset($this->table)){
            throw new Exception('$table is not set and getTable is not defined');
        }

        return $this->table;
    }

    public static function with(array $attributes = []): static
    {
        $model = new static();
        $model->attributes = $attributes;
        return $model;
    }

    public function all(): array
    {
        if (!isset($this->type)){
            $this->select();
        }
    }

    public function first(): array
    {
        if (!isset($this->type)){
            $this->select();
        }
    }

    public static function with(array $attributes = []):static
    {
        $model = new static();
        $model->attributes = $attributes;
        return $model;
    }

    public static function query(): ModelCollector|QueryBuilder
    {
        $model = new static();
        $query = $model->getConnection()->query();
        return (new ModelCollector($query, static::class))
        ->from($model->getTable());
    }

    public static function __callStatic(string $method, array $parameters = []):mixed
    {
        return static::query()->$method(...$parameters);
    }

    public function __get(string $property):mixed
    {
        if (isset($this->attributes[$property])) {
            return $this->attributes[$property];
        }

        return null;
    }

    public function __set(string $property, $value){
        $this->attributes[$property] = $value;
    }

}