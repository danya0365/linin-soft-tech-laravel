<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LinenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $insertGetId = DB::table('linen_types')->insertGetId([
            'name' => 'ผ้าขน',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'ผ้าเช็ดตัว',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'ผ้าเช็ดมือ',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'ผ้าเช็ดหน้า',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'ผ้าห่มขน',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        DB::table('linen_types')->insert([
            'name' => 'ผ้ารีด',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_types')->insert([
            'name' => 'ผ้า OR',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_types')->insert([
            'name' => 'เสื้อ',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_types')->insert([
            'name' => 'กางเกง',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_types')->insert([
            'name' => 'อื่นๆ',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }
}
