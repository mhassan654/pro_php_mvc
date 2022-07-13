<?php

namespace Framework\View;

use Closure;
use Exception;
use Framework\View\View;
use Framework\View\Engine\Engine;
use Framework\Validation\Rule\Rule;
use Framework\Validation\ValidationException;

// use Dotenv\Exception\ValidationException;


class Manager
{
    protected array $paths = [];
    protected array $engines = [];
    protected array $macros = [];
    protected array $rules = [];

    public function addRule(string $alias, Rule $rule): static
    {
        $this->rules[$alias] = $rule;
        return $this;
    }

    public function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $rulesForField) :
            foreach ($rulesForField as $rule) :
                $name = $rule;
        $params = [];

        if (str_contains($rule, ':')) :
                    [$name, $params] = explode(':', $rule);
        $params = explode(',', $params);
        endif;

        $processor = $this->rules[$name];

        if (!$processor->validate($data, $field, $params)) :
                    if (!isset($errors[$field])) :
                        $errors[$field] = [];
        endif;

        array_push($errors[$field], $processor->getMessage($data, $field, $params));
        endif;
        endforeach;
        endforeach;

        if (count($errors)) :
            $exception = new ValidationException();
        $exception->setErrors($errors);
        throw $exception;
        endif;

        return array_intersect_key($data, $rules);
    }

    public function addPath(string $path): static
    {
        array_push($this->paths, $path);
        return $this;
    }

    public function addEngine(string $extension, Engine $engine): static
    {
        $this->engines[$extension] = $engine;
        $this->engines[$extension]->setManager($this);
        return $this;
    }

    public function render(string $template, array $data = []): View
    {
        foreach ($this->engines as $extension => $engine) :
            foreach ($this->paths as $path) :
                $file = "{$path}/{$template}.{$extension}";

        if (is_file($file)) :
                    // return $engine->render($file, $data);
                    return new View($engine, realpath($file), $data);
        endif;
        endforeach;
        endforeach;

        throw new \Exception("Could not resolve '{$file}'");
    }

    /**
     * @throws Exception
     */
    public function resolve(string $template, array $data = []): View
    {
        foreach ($this->engines as $extension => $engine) :
            foreach ($this->paths as $path) :
                $file = "{$path}/{$template}.{$extension}";

        // if (is_file($file)) :
        //     return $engine->render($file, $data);
        // endif;
        if (is_file($file)) {
            return new View($engine, realpath($file), $data);
        }
        endforeach;
        endforeach;

        throw new \Exception("Could not resolve '{$template}'");
    }

    public function addMacro(string $name, Closure $closure): static
    {
        $this->macros[$name] = $closure;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function useMacro(string $name, ...$values)
    {
        if (isset($this->macros[$name])) {
            $bound = $this->macros[$name]->bindTo($this);

            return $bound(...$values);
        }
        throw new Exception("Macro isn't defined: '{$name}'");
    }
}
