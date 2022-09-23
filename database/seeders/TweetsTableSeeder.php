<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TweetsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tweets')->delete();
        
        
        
    }
}