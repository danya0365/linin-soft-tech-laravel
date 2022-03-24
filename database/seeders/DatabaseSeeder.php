<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomerGroup;
use App\Models\Customer;

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
    }
}
