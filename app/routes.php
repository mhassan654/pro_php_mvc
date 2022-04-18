<?php
use Framework\Routing\Router;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Users\RegisterUserController;
use App\Http\Controllers\Services\ShowServiceController;
use App\Http\Controllers\Products\ListProductsController;
use App\Http\Controllers\Products\ShowProductsController;
use App\Http\Controllers\Users\ShowRegisterFormController;

return function(Router $router){
    // $router->add(
    //     'GET','/',
    //     fn()=>view('home',['number'=>42]),
    // );
      $router->add(
        'GET','/',[HomeController::class,'handle'],
    )->name('show-home');

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
//        function() use ($router){
//            $parameters = $router->current()->parameters();
//            // return "product is {$parameters['product']}";
//            return view('products/view',[
//                'product'=>$parameters['product'],
//                'scary'=>'<script>alert("bool")</script>',
//            ]);
//        },
          [new ShowProductsController($router),'handle'],
    );

    $router->add(
        'GET','/services/views/{service?}',
        // function () use ($router){
        //     $parameters = $router->current()->parameters();

        //     if(empty($parameters['services'])){
        //         return 'all services';
        //     }
        //     return "services is {$parameters['services']}";
        // }
        [new ShowServiceController($router), 'handle'],
         )->name('show-service');
    );

    $router->add(
        'GET','/products/{page?}',
//        function () use ($router){
//            $parameters = $router->current()->parameters();
//            $parameters['page']??=1;
//            return "products for page {$parameters['page']}";
//        },
        [new ListProductsController($router),'handle'],
    )->name('product-list');

        $router->add(
    'GET', '/register',
    [new ShowRegisterFormController($router), 'handle'],
    )->name('show-register-form');

    $router->add(
    'POST', '/register',
    [new RegisterUserController($router), 'handle'],
    )->name('register-user');

    $router->errorHandler(404, fn()=>'whoops!');
};