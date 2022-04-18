<?php
use Framework\View;

if(!function_exists('view'))
{
    function view(string $template, array $data =[]): View\View
    {
        static $manager;

        if(!$manager){
            $manager = new View\Manager();

            // let's add a path for our views folder
            // so the manager knows where to look for views
            $manager->addPath(__DIR__ .'/../resources/views');

            // we'll also start adding new engine classes
            // with their expected extensions to be able to pick
            // the appropriate engine for the template
            $manager->addEngine('basic.php',new View\Engine\BasicEngine());
            $manager->addEngine('advanced.php',new View\Engine\AdvancedEngine());
            $manager->addEngine('php', new View\Engine\PhpEngine());

            
            $manager->addMacro('escape',fn($value) => htmlspecialchars($value));
            $manager->addMacro('includes',fn(...$params) => print view($params));
        }
//        return $manager->render($template, $data);
        return $manager->resolve($template, $data);
    }

    if(!function_exists('redirect')):
        function redirect(string $url)
        {
            header("Location: {$url}");
            exit;
        }
    endif;

    if(!function_exists('validate')):
    
        function validate(array $data, array $rules)
        {
            static $manager;
            if(!$manager):
                $manager = new Validation\Manager();

                // let's add the rules that come with the framework
                $manager->addRule('required', new Validation\Rule\RequiredRule());
                $manager->addRule('email', new Validation\Rule\EmailRule());
                $manager->addRule('min', new Validation\Rule\MinRule());
            endif;
            return $manager->validate($data, $rules);
        }
    endif;
}