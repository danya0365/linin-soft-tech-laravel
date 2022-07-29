<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use function Ramsey\Uuid\v1;

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

        $insertGetId = DB::table('linen_types')->insertGetId([
            'name' => 'ผ้ารีด',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'ปลอกหมอน',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'ผ้าปูเตียง',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'ผ้าขวาง',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'ผ้าดูเว่',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        $insertGetId = DB::table('linen_types')->insertGetId([
            'name' => 'ผ้า OR',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'เสื้อเจ้าที่หน้าที่ OR',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'กางเกงเจ้าหน้าที่ OR',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'เสื้อคนไข้ OR',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        $insertGetId = DB::table('linen_types')->insertGetId([
            'name' => 'เสื้อ',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'เสื้อกาวแพทย์',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'เสื้อยืด',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'เสื้อเด็ก',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'เสื้อคนไข้',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        $insertGetId = DB::table('linen_types')->insertGetId([
            'name' => 'กางเกง',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'กางเกงยืด',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'กางเกงเด็ก',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'กางเกงคนไข้',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        $insertGetId = DB::table('linen_types')->insertGetId([
            'name' => 'อื่นๆ',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('linen_products')->insertGetId([
            'name' => 'อื่นๆ',
            'linen_type_id' => $insertGetId,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }
}
