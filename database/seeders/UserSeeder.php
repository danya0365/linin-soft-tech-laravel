<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin.lininsofttech@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'is_can_access_admin' => 1,
            'is_can_access_supervisor' => 1,
            'is_can_access_customer' => 1,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('users')->insert([
            'name' => 'Employee',
            'email' => 'employee.lininsofttech@gmail.com',
            'role' => 'employee',
            'password' => Hash::make('12345678'),
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        DB::table('users')->insert([
            'name' => 'Customer',
            'email' => 'customer.lininsofttech@gmail.com',
            'role' => 'customer',
            'is_can_access_customer' => 1,
            'password' => Hash::make('12345678'),
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }
}
