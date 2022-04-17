<?php


namespace Framework\View;

use Exception;
use Closure;
use Framework\View\View;
use Framework\View\Engine\Engine;


class Manager
{
    protected array $paths = [];
    protected array $engines = [];
    protected array $macros = [];

    public function addPath(string $path): static
    {
        $this->paths[] = $path;
        return $this;
    }

    public function addEngine(string $extension, Engine $engine): static
    {
        $this->engines[$extension] = $engine;
        return $this;
    }

    public function render(string $template, array $data = []): string
    {

        foreach ($this->engines as $extension => $engine):
            foreach ($this->paths as $path):
                $file = "{$path}/{$template}.{$extension}";

            if (is_file($file)):
                return $engine->render($file,$data);
                endif;
                endforeach;
        endforeach;

        throw new \Exception("Could not render '{$file}'");
    }

    public function resolve(string $template, array $data = []): View
    {
        foreach ($this->engines as $extension => $engine):
            foreach ($this->paths as $path):
                $file = "{$path}/{$template}.{$extension}";

            if (is_file($file)):
                return $engine->render($file,$data);
                endif;
                endforeach;
        endforeach;

        throw new \Exception("Could not render '{$template}'");
        
    }

    public function addMacro(string $name, Closure $closure): static
    {
        $this->macros[$name] = $closure;
        return $this;
    }

    public function useMacro(string $name, ...$values)
    {
        if(isset($this->macros[$name]))
        {
            $bound = $this->macros[$name]->bindTo($this);

            return $bound(...$values);
        }
        throw new Exception("Macro isn't defined: '{$name}'");
    }

}