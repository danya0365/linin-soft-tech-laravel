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
            'icon' => 'fa-solid fa-cubes-stacked',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('inventory_groups')->insert([
            'name' => 'ถุงพลาสติก',
            'icon' => 'fa-solid fa-cubes-stacked',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('inventory_groups')->insert([
            'name' => 'วัสดุทั่วไป',
            'icon' => 'fa-solid fa-cubes-stacked',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('inventory_groups')->insert([
            'name' => 'แผนกซ่อมบำรุง',
            'icon' => 'fa-solid fa-cubes-stacked',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('inventory_groups')->insert([
            'name' => 'แก๊ส',
            'icon' => 'fa-solid fa-cubes-stacked',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('inventory_groups')->insert([
            'name' => 'ชีวมวล',
            'icon' => 'fa-solid fa-cubes-stacked',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }
}