<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FollowRequestsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('follow_requests')->delete();
        
        
        
    }
}