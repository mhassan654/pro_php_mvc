<?php


namespace Framework\Routing;


class Router
{
    protected string $method;
    protected string $path;
    protected $handler;

    public function __construct( string $method,string $path,string $handler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->handler = $handler;

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
            }catch(Throwable $e)
            {
                return $this->dispatchError();
            }
        }

    }


}