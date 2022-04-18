<?php


namespace Framework\Database;

use Closure;
use Framework\Database\Connection\Connection;


class Factory
{
    protected array $connectors;

    public function addConnectors(string $alias, Closure $connector): static
    {

        $this->connectors[$alias] = $connector;
        return $this;
    }

    public function connect(array $config): Connection
    {
        if(!isset($config['type'])):
            throw new ConnectionException('type is not defined');
        endif;

        $type = $config['type'];

        if(isset($this->connectors[$type])):
            return $this->connectors[$type]($config);
        endif;

        throw new ConnectionException('unrecognised type');
    }

}