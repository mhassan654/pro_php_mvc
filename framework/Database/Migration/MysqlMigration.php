<?php


namespace Framework\Database\Migration;


use Framework\Database\Migration\Field;
use Framework\Database\Migration\Migration;
use Framework\Database\Migration\FloatField;
use Framework\Database\Connection\Connection;
use Framework\Database\Connection\MysqlConnection;

class MysqlMigration extends Migration
{
    protected MysqlConnection $connection;
    protected string $table;
    protected string $type;
    protected array $drops = [];

    public function __construct(MysqlConnection $connection, string $table, string $type)
    {
        // TODO: Implement connection() method.
        $this->connection = $connection;
        $this->table = $table;
        $this->type = $type;
    }

    public function dropColumn(string $name): static
    {
        $this->dropa[] = $name;
        return $this;
    }

    public function execute(): void
    {
        // TODO: Implement execute() method.
        $fields = array_map(fn ($field) => $this->stringForField($field), $this->fields);
        $fields = join(',' . PHP_EOL, $fields);

        $primary = array_filter($this->fields, fn ($field) => $field
            instanceof IdField);

        $primaryKey = isset($primary[0]) ? "PRIMARY KEY ('{$primary[0]->name}`)" : '';

        if ($this->type === 'create') {
            $fields = join(PHP_EOL, array_map(fn ($field) => "{$field},", $fields));

            $query = "CREATE TABLE `{$this->table}` ({$fields}, {$primaryKey}) 
        ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        }

        if ($this->alter === 'alter') {
            $fields = join(PHP_EOL, array_map(fn ($field) => "{$field};", $fields));

            $drops = join(PHP_EOL, array_map(fn($drop)=>"DROP COLUMN `{$drop}`;",$this->drops));

            $query = "ALTER TABLE `{$this->table}` {$fields} {$drops}";
        }

        $statement = $this->connection->pdo()->prepare($query);
        $statement->execute();
    }

    private function stringForField(Field $field): string
    {
        if ($field instanceof BoolField) :
            $template = "`{$field->name}` tinyint(4)";

            if ($field->nullable) :
                $template .= "DEFAULT NULL";
            endif;

            if ($field->default !== null) :
                $default = (int) $field->default;
                $template .= "DEFAULT {$default}";
            endif;

            return $template;

        endif;

        if ($field instanceof DateTimeField) :
            $template = "`{$field->name}` datetime";

            if ($field->nullable) :
                $template .= "DEFAULT NULL";
            endif;
            // endif;

            if ($field->default === 'CURRENT_TIMESTAMP') :
                $template .= " DEFAULT CURRENT_TIMESTAMP";

            endif;

            if ($field->default !== null) :
                $template .= " DEFAULT '{$field->default}'";

            endif;
            return $template;
        endif;

        if ($field instanceof FloatField) :
            $template = "`{$field->name}` float";
            // }

            if ($field->nullable) :
                $template .= " DEFAULT NULL";

                // }

                if ($field->default !== null) :
                    $template .= " DEFAULT '{$field->default}'";

                endif;
                return $template;

            endif;

            if ($field instanceof IdField) :
                return "`{$field->name}` int(11) unsigned NOT NULL AUTO_INCREMENT";

            endif;

            if ($field instanceof IntField) :
                $template = "`{$field->name}` int(11)";

                if ($field->nullable) :
                    $template .= " DEFAULT NULL";
                endif;

                if ($field->default !== null) :
                    $template .= " DEFAULT '{$field->default}'";
                endif;

                return $template;
            endif;
            // }
            if ($field instanceof StringField) :
                $template = "`{$field->name}` varchar(255)";

                if ($field->nullable) :
                    $template .= " DEFAULT NULL";
                endif;

                if ($field->default !== null) :
                    $template .= " DEFAULT '{$field->default}'";
                endif;
                return $template;
            endif;

            if ($field instanceof TextField) :
                return "`{$field->name}` text";
            endif;
        // throw new MigrationException("Unrecognised field type for {$field->name}");
        endif;
        // endif;
    }

    // public function execute()
    // {
    //     $field = array_map(fn($field) => $this->stringForField($field),
    //     $this->fields);
    // }

    /**
     * @return Connection
     */
    public function connection(): Connection
    {
        // TODO: Implement connection() method.
    }
}
