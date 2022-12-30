<?php

namespace Framework\Validation\Rule;

class EmailRule implements Rule
{
    public function validate(array $data, string $field, array $params)
    {
        // TODO: Implement validate() method.
        if (empty($data[$field])):
            return true;
        endif;
        return str_contains($data[$field], '@');
    }

    public function getMessage(array $data, string $field, array $params)
    {
        // TODO: Implement getMessage() method.
        return "{$field} should be an email";
    }
}
