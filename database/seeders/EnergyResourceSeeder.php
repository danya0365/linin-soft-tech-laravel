<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnergyResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('energy_resources')->insert([
            'name' => 'น้ำ',
            'var_name' => 'water',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('energy_resources')->insert([
            'name' => 'ไฟฟ้า',
            'var_name' => 'electricity',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('energy_resources')->insert([
            'name' => 'แก๊ส',
            'var_name' => 'gas',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('energy_resources')->insert([
            'name' => 'ชีวมวล',
            'var_name' => 'biomass',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('energy_resources')->insert([
            'name' => 'น้ำมันเตา',
            'var_name' => 'fuel_oil',
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }
}