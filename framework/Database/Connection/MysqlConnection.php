<?php

namespace Framework\Database\Connection;

use Pdo;
use InvalidArgumentException;
use Framework\Database\Migration\Field;
use Framework\Database\Migration\IdField;
use Framework\Database\Migration\IntField;
use Framework\Database\Migration\BoolField;
use Framework\Database\Migration\Migration;
use Framework\Database\Migration\TextField;
use Framework\Database\Migration\FloatField;
use Framework\Database\Connection\Connection;
use Framework\Database\Migration\StringField;
use Framework\Database\Migration\DateTimeField;
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

        if (empty($host) || empty($database) || empty($username)) :
            throw new InvalidArgumentException('Connection Incorrectly configured');
        endif;

        $this->pdo = new Pdo("mysql:host={$host};port={$port};dbname={$database}", $username, $password);
    }

    /**0700447559
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
     * @return Connection
     */
    public function connection(): Connection
    {
        // TODO: Implement connection() method.
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

    public function alterTable(string $table): MysqlMigration
    {
        return new MysqlMigration($this, $table, 'alter');
    }

    private function stringForField(Field $field): string
    {
        $prefix = '';
        if ($this->type === 'alter') {
            $prefix = 'ADD';
        }

        if ($field->alter) {
            $prefix = 'MODIFY';
        }

        if ($field instanceof BoolField) {
            $template = "{$prefix} `{$field->name}` tinyint(4)";
            if ($field->nullable) {
                $template .= " DEFAULT NULL";
            }

            if ($field->default !== null) {
                $default = (int) $field->default;
                $template .= " DEFAULT {$default}";
            }

            return $template;
        }

        if ($field instanceof DateTimeField) {
            $template = "{$prefix} `{$field->name}` datetime";

            if ($field->nullable) {
                $template .= "DEFAULT NULL";
            }

            if ($field->default === 'CURRENT_TIMESTAMP') {
                $template .= " DEFAULT CURRENT_TIMESTAMP";
            } elseif ($field->default !== null) {
                $template .= "DEFAULT '{$field->default}'";
            }

            return $template;
        }

        if ($field instanceof FloatField) {
            $template = "{$prefix} `{$field->name}` float";

            if ($field->nullable) {
                $template .= "DEFAULT NULL";
            }

            if ($field->default !== null) {
                $template .= "DEFAULT '{$field->default}'";
            }
            return $template;
        }

        if ($field instanceof IdField) {
            return "{$prefix} `{$field->name}` int(11) unsigned NOT NULL AUTO_
            INCREMENT";
        }
        if ($field instanceof IntField) {
            $template = "{$prefix} `{$field->name}` int(11)";
            if ($field->nullable) {
                $template .= " DEFAULT NULL";
            }
            if ($field->default !== null) {
                $template .= " DEFAULT '{$field->default}'";
            }
            return $template;
        }
        if ($field instanceof StringField) {
            $template = "{$prefix} `{$field->name}` varchar(255)";
            if ($field->nullable) {
                $template .= " DEFAULT NULL";
            }
            if ($field->default !== null) {
                $template .= " DEFAULT '{$field->default}'";
            }
            return $template;
        }
        if ($field instanceof TextField) {
            return "{$prefix} `{$field->name}` text";
        }

        throw new MigrationException("Unrecognized field type for {$field->name}");
    }

    public function getTables(): array
    {
        return [];
    }

    public function hasTable($name): bool
    {
        return true;
    }

    public function dropTables(): int
    {
        return 1;
    }
}
