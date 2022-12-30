<?php

namespace Framework\Database\Migration;

class IdField extends Field
{
    public function default()
    {
        throw new MigrationException('ID fields cannot have a default value');
    }
}
