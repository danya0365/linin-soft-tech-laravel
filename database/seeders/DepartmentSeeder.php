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
            'name' => 'รับสินค้า'
        ]);
        DB::table('departments')->insert([
            'name' => 'ซัก'
        ]);
        DB::table('departments')->insert([
            'name' => 'อบ'
        ]);
        DB::table('departments')->insert([
            'name' => 'รีด'
        ]);
        DB::table('departments')->insert([
            'name' => 'พับแพ็ค'
        ]);
        DB::table('departments')->insert([
            'name' => 'จัดเก็บ'
        ]);
    }
}
