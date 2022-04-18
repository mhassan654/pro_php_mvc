<?php
require_once __DIR__ .'./vendor/autoload.php';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestPath = $_SERVER['REQUEST_URI'] ?? '/';

//function redirectForeverTo($path)
//{
//    header('Location: {$path}', $replace = true, $code =301);
//    exit;
//}
//
//if($requestMethod === 'GET' and $requestPath === '/'){
//    print <<<HTML
//<!doctype html>
//<html lang="en">
//<body>
//hello saava v2
//</body>
//</html>
//HTML;
//}else if($requestPath === '/old-home'){
////    header('Location: /', $replace = true, $code =301);
//    redirectForeverTo('/');
//    exit;
//
//}else{
//    include(__DIR__.'/includes/404.php');
//
//}