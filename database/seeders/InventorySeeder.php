<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $insertGetId = DB::table('inventory_groups')->insertGetId([
            'name' => 'เคมี/ผงซักฟอก',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('inventories')->insert([
            'name' => 'ผงซักฟอก 200 ลิตร',
            'inventory_group_id' => $insertGetId,
            'unit'  => 'ถัง',
            'total_quantity' => 100,
            'remain_quantity' => 100,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('inventory_groups')->insert([
            'name' => 'ถุงพลาสติก',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('inventory_groups')->insert([
            'name' => 'วัสดุทั่วไป',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('inventory_groups')->insert([
            'name' => 'แผนกซ่อมบำรุง',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('inventory_groups')->insert([
            'name' => 'แก๊ส',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('inventory_groups')->insert([
            'name' => 'ชีวมวล',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }
}
