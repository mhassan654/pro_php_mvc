<?php


namespace App\Http\Controllers\Users;

use Framework\Routing\Router;


class RegisterUserController
{
    protected Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        secure();
        
        $data = validate($_POST,[
            'name'=>['required'],
            'email'=>['required','email'],
            'password'=>['required','min:10'],
        ]);

        // use $data to create a db record
        $_SESSION['registered'] = true;
        return redirect($this->router->route('home-page'));
    }

}