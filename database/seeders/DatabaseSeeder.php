<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomerGroup;
use App\Models\Customer;
use App\Models\Department;
use App\Models\DryerMachine;
use App\Models\Employee;
use App\Models\WashingMachine;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DryerMachine::factory()
            ->count(30)
            ->sequence(fn ($sequence) => ['name' => 'เครื่องอบผ้า ' . $sequence->index + 1, 'photo' => 'bi-server'])
            ->create();
        $this->call([
            UserSeeder::class,
            DepartmentSeeder::class,
            LinenSeeder::class,
            EnergyResourceSeeder::class,
            InventorySeeder::class,
            CustomerSeeder::class
        ]);

        Customer::factory()
            ->count(20)
            //->sequence(fn ($sequence) => ['name' => 'โรงพยาบาล ' . $sequence->index, 'group' => CustomerGroup::all()->random()])
            ->sequence(fn ($sequence) => ['customer_group_id' => CustomerGroup::all()->random()->id])
            ->create();

        Employee::factory()
            ->count(100)
            ->sequence(fn ($sequence) => [
                'department_id' => Department::all()->random()->id,
                'code' => sprintf('%05d', $sequence->index + 1),
                'photo' => 'bi-person-circle',
                'password' => '1234'
            ])
            ->create();

        WashingMachine::factory()
            ->count(30)
            ->sequence(fn ($sequence) => ['name' => 'เครื่องซักผ้า ' . $sequence->index + 1, 'photo' => 'bi-server'])
            ->create();

        DryerMachine::factory()
            ->count(30)
            ->sequence(fn ($sequence) => ['name' => 'เครื่องอบผ้า ' . $sequence->index + 1, 'photo' => 'bi-server'])
            ->create();
    }
}
