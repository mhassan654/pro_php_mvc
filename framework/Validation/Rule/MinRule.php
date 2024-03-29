<?php

namespace Framework\Validation\Rule;

use InvalidArgumentException;

class MinRule implements Rule
{
    public function validate(array $data, string $field, array $params)
    {
        // TODO: Implement validate() method.
        if (empty($data[$field])) :
            return true;
        endif;

        if (empty($params[0])) :
            throw new InvalidArgumentException('specify a min length');
        endif;

        $length = (int) $params[0];
        strlen($data[$field]) >= $length;
    }

    public function getMessage(array $data, string $field, array $params)
    {
        // TODO: Implement getMessage() method.
        $length = (int) $params[0];
        return "{$field} should be at least {$length} characters";
    }
}
