<?php

namespace Framework\Validation\Rule;

class RequiredRule implements Rule
{
    public function validate(array $data, string $field, array $params)
    {
        // TODO: Implement validate() method.
        return !empty($data[$field]);
    }

    public function getMessage(array $data, string $field, array $params)
    {
        // TODO: Implement getMessage() method.
        return "{$field} is required";
    }
}
