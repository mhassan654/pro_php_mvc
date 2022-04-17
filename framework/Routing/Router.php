<?php


namespace Framework\Routing;
use Exception;


class Router
{
    protected array $errorHandler = [];
    protected string $method;
    protected string $path;
    protected $handler;

    public function __construct( string $method,string $path,string $handler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->handler = $handler;

    }

    public function route(string $name, array $parameters=[]): string
    {
        foreach($this->routes as $route)
        {
            if($route->name() === $name)
            {
                
            }

        }

    }

    public function dispatch()
    {
        $paths = $this->paths();

        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestPath = $_SERVER['REQUEST_URI'] ?? '/';

        // this looks through the defined routes and retruns
        // the first that matches the requested method and path
        $matching = $this->match($requestMethod, $requestPath);

        if($matching){
            try{
                // this action could throw an exception
                // so catch it and display the global error
                //page that we wi;; define in the orutes file
                return $matching->dispatch();
            }catch(\Throwable $e)
            {
                return $this->dispatchError();
            }
        }

        // if the path is defined for a different method
        //we can throw a unique error page for it
        if(in_array($requestPath, $path)){
            return $this->dispatchNotAllowed();
        }
        return $this->dispatchNotFound();

    }

    private function paths():array
    {
        $paths = [];
        foreach($paths->routes as $route){
            $paths[] = $route->paths();
        }
        return $paths;

    }

    private function match(string $method, string $path): ?Route
    {
        foreach($this->routes as $route){
            if($route->matches($method, $path))
            {
                return $route;
            }
        }
        return null;
    }

    public function errorHandler(int $code, callable $handler)
    {
        $this->errorHandlers[$code] = $handler;
    }

    private function dispatchNotFound()
    {
        $this->errorHandlers[404] ?? = fn()=>"not found";
        return $this->errorHandlers[404]();
    }

    private function dispatchNotAllowed()
    {
        $this->errorHandlers[400] ?? = fn()=>"not allowed";
        return $this->errorHandlers[400]();
    }

    private function dispatchError()
    {
        $this->errorHandlers[500] ?? = fn()=>"server error";
        return $this->errorHandlers[500]();
    }

    public function redirect($path)
    {
        header("Location: {$path}", $replace=true, $code=301);
        exit;
    }


}