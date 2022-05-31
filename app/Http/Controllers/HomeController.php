<?php


namespace App\Http\Controllers;

use Framework\Database\Factory;
use Framework\Database\Connection\MysqlConnection;


class HomeController
{

    public function handle()
    {
        $factory = new Factory();
        $factory->addConnector('mysql', function ($config) {
            return new MysqlConnection($config);
        });
        $connection = $factory->connect([
            'type' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'promvc',
            'username' => 'root',
            'password' => '',
        ]);

        $product = $connection
            ->query()
            ->select()
            ->from('products')
            ->first();
        $products = json_encode($product);
//        print_r($product);
        return view('home', ['number' => 42,'featured' => $products]);
    }
}
