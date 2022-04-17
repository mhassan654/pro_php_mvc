<?php
use Framework\Routing\Router;

return function(Router $router){
    $router->add(
        'GET','/',
        // fn()=>'heelo saava',
        fn()=>view('home',['number'=>42]),
    );

    $router->add(
        'GET','/old-home',
        fn() => $router->redirect('/'),
    );
    $router->add(
        'GET', 'has-server-error',
        // fn() => throw new Exception();
        fn()=> throw new Exception()
        
    );

    $router->add(
        'GET', '/has-validation-error',
        fn()=>$router->dispatchNotAllowed(),
    );

    $router->add(
        'GET', '/products/view/{product}',
        function() use ($router){
            $parameters = $router->current()->parameters();
            return "product is {$parameters['product']}";
        },
    );

    $router->add(
        'GET','/services/views/{service?}',
        function () use ($router){
            $parameters = $router->current()->parameters();

            if(empty($parameters['services'])){
                return 'all services';
            }
            return "services is {$parameters['services']}";
        }
    );

    $router->add(
        'GET','/products/{page?}',
        function () use ($router){
            $parameters = $router->current()->parameters();
            $parameters['page']??=1;
            return "products for page {$parameters['page']}";
        },
    )->name('product-list');

    $router->errorHandler(404, fn()=>'whoops!');
};