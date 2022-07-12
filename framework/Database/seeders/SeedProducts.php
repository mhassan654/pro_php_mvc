<?php

namespace Framework\Database\seeders;

use Framework\Database\Connection\Connection;

class SeedProducts
{
    public function migrate(Connection $connection)
    {
        $products =[
            ['name'=>'space tour',
                'description' => 'Take a trip on a rocket ship. Our tours
are out of this world. Sign up now for a journey you
won&apos;t soon forget.',
                ],
            [
                'name' => 'Large Rocket',
                'description' => 'Need to bring some extra space-baggage?
Everyone asking you to bring back a moon rock for them?
This is the rocket you want...',
            ],
        ];

        foreach ($products as $product)
        {
            $connection
                ->query()
                ->from('products')
                ->insert(['name', 'description'], $product);
        }
    }

}