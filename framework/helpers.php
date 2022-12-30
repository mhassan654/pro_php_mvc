<?php

use Framework\View;
use Framework\View\Manager;

if (!function_exists('view')) {
    function view(string $template, array $data =[]): View\View
    {
        static $manager;

        if (!$manager) {
            $manager = new View\Manager();

            // let's add a path for our views folder
            // so the manager knows where to look for views
            $manager->addPath(__DIR__ .'/../resources/views');

            // we'll also start adding new engine classes
            // with their expected extensions to be able to pick
            // the appropriate engine for the template
            $manager->addEngine('basic.php', new View\Engine\BasicEngine());
            $manager->addEngine('advanced.php', new View\Engine\AdvancedEngine());
            $manager->addEngine('php', new View\Engine\PhpEngine());


            $manager->addMacro('escape', fn ($value) => htmlspecialchars($value));
            $manager->addMacro('includes', fn (...$params) => print view($params));
        }
//        return $manager->render($template, $data);
        return $manager->resolve($template, $data);
    }

    if (!function_exists('redirect')):
        function redirect(string $url)
        {
            header("Location: {$url}");
            exit;
        }
    endif;

    // form field validation function
    if (!function_exists('validate')):

        function validate(array $data, array $rules)
        {
            static $manager;
            if (!$manager):
                $manager = new Manager();

            // let's add the rules that come with the framework
            $manager->addRule('required', new \Framework\Validation\Rule\RequiredRule());
            $manager->addRule('email', new \Framework\Validation\Rule\EmailRule());
            $manager->addRule('min', new \Framework\Validation\Rule\MinRule());
            endif;
            return $manager->validate($data, $rules);
        }
    endif;

    if (!function_exists('csrf')):
        function csrf()
        {
            $_SESSION['token'] = bin2hex(random_bytes(32));
            return $_SESSION['token'];
        }
    endif;

    // form cross-site refrence token function
    if (!function_exists('csrf')):
        function csrf()
        {
            $_SESSION['token'] = bin2hex(random_bytes(32));
            return $_SESSION['token'];
        }
    endif;

    if (!function_exists('secure')):
        function secure()
        {
            if (!isset($_POST['csrf']) || !isset($_SESSION['token']) ||
        !hash_equals($_SESSION['token'], $_POST['csrf'])):
            throw new Exception('CSRF token mismatch');
            endif;
        }
    endif;
}
