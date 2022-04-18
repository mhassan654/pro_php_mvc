<?php


namespace App\Http\Controllers;


class HomeController
{
    public function handle()
    {
        return view('home',['number' => 42]);
    }

}