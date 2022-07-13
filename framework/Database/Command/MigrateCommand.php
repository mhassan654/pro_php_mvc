<?php

namespace Framework\Database\Command;

use Framework\Database\Connection\Connection;
use Framework\Database\Connection\MysqlConnection;
use Framework\Database\Connection\SqliteConnection;
use Framework\Database\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected static $defaultName = 'migrate';
    protected array $wheres =[];

    protected function configure()
    {
//        parent::configure(); // TODO: Change the autogenerated stub
        $this
            ->setDescription('Migrates the database')
            ->setHelp('This command takes an optional name and returns it in uppercase.
            If no name is provided, "stranger" is used.');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->connection();

        $current = getcwd();
        $pattern = 'framework/database/migrations/*.php';

        $paths = glob("{$current}/{$pattern}");
        // print_r($paths);

        if (count($paths) < 1):

           $output->writeln('No migrations found');

        return Command::SUCCESS;
        endif;

        if ($input->getOptions('--fresh')) {
            $output->writeln('Dropping existing database migrations');

            $connection->dropTables();
            $connection = $this->connection();
        }

        if (!$connection->hasTable('migrations')) {
            $output->writeln('Creating migration tables');
            $this->createMigrationsTable($connection);
        }

        foreach ($paths as $path) {
            [$prefix, $file] = explode('_', $path);
            [$class, $extension] = explode('.', $prefix);
            require $path;
            $obj = new $class();
            $obj->migrate($connection);

            $connection->query()->from('migrations')
        ->insert(['name'], ['name'=>$class]);
        }

        return Command::SUCCESS;
    }

    private function createMigrationsTable(Connection $connection)
    {
        $table = $connection->createTable('migrations');
        $table->id('id');
        $table->string('name');
        $table->execute();
    }

    public function all():array
    {
        $statement = $this->prepare();
        $statement->execute($this->getWhereValues());

        return $statement->fetchAll(Pdo::FETCH_ASSOC);
    }

    protected function getWhereValues(): array
    {
        $values =[];
        if (count($this->wheres) === 0){
            return $values;
        }

        foreach ($this->wheres as $where){
            $values[$where[0]] = $where[2];
        }

        return $values;
    }

    public function prepare(): \PDOStatement
    {
        $query ='';
        if ($this->type === 'select'){
            $query = $this->compileSelect($query);
            $query = $this->compileWheres($query);
            $query = $this->compileLimit($query);
        }
        return $query;
    }
    protected function compileWheres(string $query): string
    {
        if (count($this->wheres) === 0) {
            return $query;
        }
        $query .= ' WHERE';
        foreach ($this->wheres as $i => $where) {
            if ($i > 0) {
                $query .= ', ';
            }
            [$column, $comparator, $value] = $where;
            $query .= " {$column} {$comparator} :{$column}";
        }
        return $query;
    }

    public function first(): ?array
    {
        $statement = $this->take(1)->prepare();
        $statement->execute($this->getWhereValues());
        $result = $statement->fetchAll(\Pdo::FETCH_ASSOC);
        if (count($result) === 1) {
            return $result[0];
        }
        return null;
    }

    private function connection(): Connection
    {
        $factory = new Factory();

        $factory->addConnector('mysql', function ($config) {
            return new MysqlConnection($config);
        });

        $factory->addConnector('sqlite', function ($config) {
            return new SqliteConnection($config);
        });

        $config = require getcwd().'./config/database.php';

        return $factory->connect($config[$config['default']]);
    }


}
