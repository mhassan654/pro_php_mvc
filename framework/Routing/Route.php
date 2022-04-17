<?php


namespace Framework\Routing;


class Route
{
    protected array $parameters =[];
    protected string $method;
    protected string $path;
    private $handler;

    public function __construct(string $method, string $path, callable $handler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->handler = $handler;
    }

    public function parameters():array
    {
        return $this->parameters;
    }
    public function method(string $method): string
    {
        return $this->method;
    }

    public function path(string $path): string
    {
        return $this->path;
    }

    public function matches(string $method, string $path): bool
    {
        //if there's a literal match then don't waste any time
//        trying to match with a regular expresssion
        if (
            $this->method === $method && $this->path === $path
        ){
            return true;
        }
        $parameterNames =[];

//        the normalisePath method ensures there's a '/'
//        before and after the path, while also removing duplicate
//        '/' characters
//        examples
//        '' becomes '/'
//        'home' becomes '/home/'
//        'product/{id}' becomes '/product/{id}/'
        $pattern = $this->normalisePath($this->path);

//        get all the parameter names and replace them with
//        regular expression syntanx, to match optional or required parameters

        $pattern = preg_replace_callback(
            '#{([^}]+)}/#',
            function (array $found) use (&$parameterNames){
                $parameterNames[] = rtrim($found[1], '?');

//                if it's an optional parameter, we make the
//                following slash optional as well'
                if (str_ends_with($found[1],'?')){
                    return '([^/]*)(?:/?)';
                }
                return '([^/]+)/';
            },
            $pattern,
        );

        if(!str_contains($pattern, '+') && !str_contains($pattern, '*'))
        {
            return false;
        }

        preg_match_all("#{$pattern}#", $this->normalisePath($path),$matches);

        $parametrValues=[];
        if(count($matches[1])>0){
//            if the route matches the request path then
//            we need to aassemble the parameters before
//            we can return true for the match
            foreach ($matches[1] as $value){
                $parametrValues[] = $value;
            }

//            make an empty array so  that we can still
//            call array_combine with optional parameters
//            which may not have been provided
            $emptyVAlues = array_fill(
                0, count($parameterNames),null
            );

            //+= syntax for arrays means: take valuse fromt he right-hand
//            side and only add them to the left-hand side if the same
//            key doesn't already exist.
//
//            you'// usually want to use array_merge to combine arrays,
//            but this is an interesting use for +=
            $parameterNames += $emptyVAlues;
            $this->parameters = array_combine($parameterNames,$parametrValues);
            return true;
        }
        return false;
}

    public function dispatch()
    {
        return call_user_func($this->handler);
    }

    private function normalisePath(string $path): string
    {
        $path = trim($path,'/');
        $path = "/{$path}/";
        
        // remove multiple '/' in a row
        $path = preg_replace('/[\/]{2,}/','/',$path);
        return $path;
    }

    public function name(string $name = null): mixed
    {
        if($name){
            $this->name = $name;
            return $this;
        }

        return $this->name;
    }

}