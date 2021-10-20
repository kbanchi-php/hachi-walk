<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            ['name' => '観光'],
            ['name' => '飲食'],
            ['name' => '買い物'],
            ['name' => '温泉'],
            ['name' => '地域交流'],
            ['name' => 'その他'],
        ];
        DB::table('categories')->insert($param);
    }
}
