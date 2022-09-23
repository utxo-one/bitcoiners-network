<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TransactionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('transactions')->delete();
        
        \DB::table('transactions')->insert(array (
            0 => 
            array (
                'amount' => 200,
                'created_at' => '2022-09-11 01:38:54',
                'description' => 'Lightning Deposit Reference ID: HRZQMQawfj4Nrowu74zQ1p',
                'id' => 1,
                'status' => 'final',
                'type' => 'credit',
                'updated_at' => '2022-09-11 01:38:54',
                'user_id' => 1558929312547577858,
            ),
            1 => 
            array (
                'amount' => 200,
                'created_at' => '2022-09-11 01:39:21',
                'description' => 'Lightning Deposit Reference ID: HRZQMQawfj4Nrowu74zQ1p',
                'id' => 2,
                'status' => 'final',
                'type' => 'credit',
                'updated_at' => '2022-09-11 01:39:21',
                'user_id' => 1558929312547577858,
            ),
            2 => 
            array (
                'amount' => 200,
                'created_at' => '2022-09-11 01:45:41',
                'description' => 'Lightning Deposit Reference ID: T6VfdbEMd3rxAx2nikftgt',
                'id' => 3,
                'status' => 'final',
                'type' => 'credit',
                'updated_at' => '2022-09-11 01:45:41',
                'user_id' => 1558929312547577858,
            ),
            3 => 
            array (
                'amount' => 200,
                'created_at' => '2022-09-11 01:46:22',
                'description' => 'Lightning Deposit Reference ID: T6VfdbEMd3rxAx2nikftgt',
                'id' => 4,
                'status' => 'final',
                'type' => 'credit',
                'updated_at' => '2022-09-11 01:46:22',
                'user_id' => 1558929312547577858,
            ),
            4 => 
            array (
                'amount' => 200,
                'created_at' => '2022-09-11 01:46:36',
                'description' => 'Lightning Deposit Reference ID: T6VfdbEMd3rxAx2nikftgt',
                'id' => 5,
                'status' => 'final',
                'type' => 'credit',
                'updated_at' => '2022-09-11 01:46:36',
                'user_id' => 1558929312547577858,
            ),
            5 => 
            array (
                'amount' => 100,
                'created_at' => '2022-09-11 01:55:43',
                'description' => 'Lightning Deposit Reference ID: BHu7gzw747f5RFq81H1rfh',
                'id' => 6,
                'status' => 'final',
                'type' => 'credit',
                'updated_at' => '2022-09-11 01:55:43',
                'user_id' => 1558929312547577858,
            ),
        ));
        
        
    }
}