<?php

namespace Framework\Database\QueryBuilder;

use Framework\Database\Connection\Connection;
use Framework\Database\Exception\QueryException;
use PdoStatement;
use Pdo;

abstract class QueryBuilder
{
    protected string $type;
    protected string $columns;
    protected string $table;
    protected int $limit;
    protected int $offset;
    protected array $wheres =[];

    /**
     * Get the underlying Connection instance for this query
     */
//    abstract public function connection(): Connection;

    /**
     * Fetch all rows matching the current query
     */
    public function all(): array
    {
        $statement = $this->prepare();
        $statement->execute($this->getWhereValues());

        return $statement->fetchAll(Pdo::FETCH_ASSOC);
    }

    protected function getWhereValues():array{
        $values =[];
        if(count($this->wheres) === 0){
            return $values;
        }

        foreach($this->wheres as $where){
            $values[$where[0]] = $where[2];
        }

        return $values;
    }

    public function insert(array $columns, array $values): int
    {
        $this->type = 'insert';
        $this->columns = $columns;
        $this->values = $values;
        $statement = $this->prepare();
        return $statement->execute($values);
    }

    /**
     * Prepare a query against a particular connection
     */
    public function prepare(): PdoStatement
    {
        $query = '';
        if ($this->type === 'SELECT') :
            $query = $this->compileSelect($query);
            $query = $this->compileWheres($query);
        $query = $this->compileLimit($query);
        endif;

        if ($this->type === 'insert') {
            $query = $this->compileInsert($query);
        }

        if (empty($query)) :
            throw new QueryException('Unrecognised query type');
        endif;



        return $this->connection()->pdo()->prepare($query);
    }

    /**
     * Add select clause to the query
     * @param string $query
     * @return string
     */
    protected function compileSelect(string $query): string
    {
        $query .= " SELECT {$this->columns} FROM {$this->table}";
        return $query;
    }

    /**
     * Add limit and offset clauses to the query
     * @param string $query
     * @return string
     */
    protected function compileLimit(string $query): string
    {
        if ($this->limit) :
            $query .= " LIMIT {$this->limit}";
        endif;

        if ($this->offset) :
            $query .= " OFFSET {$this->offset}";
        endif;

        return $query;
    }

    protected function compileWheres(string $query):string
    {
        if(count($this->wheres) === 0){
            return $query;
        }

        $query .='WHERE';

        foreach ($this->wheres  as $i => $where) {
            if($i > 0){
                $query .= ', ';
            }

            [$column, $comparator, $value]=$where;

            $query .= " {$column} {$comparator} : {$column}";
        }

        return $query;
    }

    /**
     * Fetch the first row matching the current query
     */
    public function first()
    // public function first(): array
    {
        $statement = $this->take(1)->prepare();
        $statement->execute($this->getWhereValues());

        $result =  $statement->fetchAll(Pdo::FETCH_ASSOC);

        if (count($result) ===1) {
            return $result[0];
        }

        return null;
    }

    /**
     * Limit a set of query results so that it's possible
     * to fetch a single or limited batch of rows
     * @param int $limit
     * @param int $offset
     * @return QueryBuilder
     */
    public function take(int $limit, int $offset = 0): static
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    /**
     * Indicate which table the query is targeting
     * @param string $table
     * @return QueryBuilder
     */
    public function from(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Indicate the query type is a "select" and remember
     * which fields should be returned by the query
     * @par
     * @return QueryBuilder
     */
    public function select(string $columns = '*'): static
    {
        $this->type = 'SELECT';
        $this->columns = $columns;
        return $this;
    }

    private function compileInsert(string $query): string
    {
        $joinedColumns = join(',', $this->columns);
        $joinedPlaceholders = join(', ', array_map(
            fn ($column) => ":{$column}",
            (array)$this->columns
        ));
        $query .= " INSERT INTO {$this->table} ({$joinedColumns}) VALUES
                ({$joinedPlaceholders})";
        return $query;
    }
}
