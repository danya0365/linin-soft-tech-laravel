<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departments')->insert([
            'var_name' => 'pickup',
            'name' => 'รับสินค้า',
            'input_unit' => 'weight',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('departments')->insert([
            'var_name' => 'wash',
            'name' => 'ซัก',
            'input_unit' => 'weight',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('departments')->insert([
            'var_name' => 'dry',
            'name' => 'อบ',
            'input_unit' => 'weight',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('departments')->insert([
            'var_name' => 'iron',
            'name' => 'รีด',
            'input_unit' => 'piece',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('departments')->insert([
            'var_name' => 'packing',
            'name' => 'พับแพ็ค',
            'input_unit' => 'piece',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('departments')->insert([
            'var_name' => 'collect',
            'name' => 'จัดเก็บ',
            'input_unit' => 'weight',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('departments')->insert([
            'var_name' => 'deliver',
            'name' => 'จัดส่ง',
            'input_unit' => 'pack',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }
}