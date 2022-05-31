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
        $statement->execute();

        return $statement->fetchAll(Pdo::FETCH_ASSOC);
    }

    /**
     * Prepare a query against a particular connection
     */
    public function prepare(): PdoStatement
    {
        $query = '';
        if ($this->type === 'SELECT') :
            $query = $this->compileSelect($query);
            $query = $this->compileLimit($query);
        endif;

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

    /**
     * Fetch the first row matching the current query
     */
    public function first(): array
    {
        $statement = $this->take(1)->prepare();
        $statement->execute();
        return $statement->fetchAll(Pdo::FETCH_ASSOC);
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
}
