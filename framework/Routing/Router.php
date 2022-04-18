<?php


namespace Framework\Routing;

use Exception;
use Framework\Validation\ValidationException;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Router
{
    protected array $errorHandler = [];
    protected string $method;
    protected string $path;
    protected  $handler;
    protected ?string $name = null;

    // public function __construct( string $method,string $path, $handler)
    // {
    //     $this->method = $method;
    //     $this->path = $path;
    //     $this->handler = $handler;

    // }

    public function route(string $name, array $parameters = []): string
    {
        foreach ($this->routes as $route) {
            if ($route->name() === $name) {
                $finds = [];
                $replaces = [];

                foreach ($this->parameters as $key => $value) {
                    // one set for required parameters
                    array_push($finds, "{{$key}}");
                    array_push($replaces, $value);

                    // ..and another for optional parameters
                    array_push($finds, "{{$key}?}");
                    array_push($replaces, $value);
                }

                $path = $route->path();
                $path = str_replace($finds, $replaces, $path);

                //remove any optional parameters not provided
                $path = preg_replace('#{[^}]+}#', '', $path);

                // we should think about warnign if a requuried parameter is not 
                // provided...
                return $path;
            }
        }
        throw new Exception('no route with that name');
    }


    public function add(string $method, string $path, $handler): Route
    {
        $route = $this->routes[] = new Route($method, $path, $handler);
        return $route;
    }

    public function dispatch()
    {
        $paths = $this->paths();

        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestPath = $_SERVER['REQUEST_URI'] ?? '/';

        // this looks through the defined routes and retruns
        // the first that matches the requested method and path
        $matching = $this->matchAll($requestMethod, $requestPath);

        if ($matching) {
            $this->current = $matching;
            try {
                // this action could throw an exception
                // so catch it and display the global error
                //page that we wi;; define in the orutes file
                return $matching->dispatch();
            } catch (\Throwable $e) {
                if ($e instanceof ValidationException) :
                    $_SESSION['errors'] = $e->getErrors();
                    return redirect($_SERVER['HTTP_REFERER']);
                endif;

                if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'dev') :
                    $whoops = new Run();
                    $whoops->pushHandler(new PrettyPageHandler());
                    $whoops->register();
                    throw $e;
                endif;

                return $this->dispatchError();
            }
        }

        // if the path is defined for a different method
        //we can throw a unique error page for it
        if (in_array($requestPath, $paths)) {

            return $this->dispatchNotAllowed();
        }
        return $this->dispatchNotFound();
    }

    private function matchAll(string $method, string $path): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->matches($method, $path)) {
                return $route;
            }
        }
        return null;
    }

    private function paths(): array
    {
        $paths = [];
        foreach ($this->routes as $route) {
            $paths[] = $route->path();
        }
        return $paths;
    }


    public function errorHandler(int $code, callable $handler)
    {
        $this->errorHandlers[$code] = $handler;
    }

    private function dispatchNotFound()
    {
        $this->errorHandlers[404] ??= fn () => "not found";
        return $this->errorHandlers[404]();
    }

    public function dispatchNotAllowed()
    {
        $this->errorHandlers[400] ??= fn () => "not allowed";
        return $this->errorHandlers[400]();
    }

    private function dispatchError()
    {
        $this->errorHandlers[500] ??= fn () => "server error";
        return $this->errorHandlers[500]();
    }

    public function redirect($path)
    {
        header("Location: {$path}", $replace = true, $code = 301);
        exit;
    }

    public function current(): ?Route
    {
        return $this->current;
    }
}
