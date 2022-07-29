<?php

namespace Database\Seeders;

use App\Models\Truck;
use Illuminate\Database\Seeder;

class TruckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Truck::factory()
            ->count(30)
            ->sequence(fn ($sequence) => ['name' => 'รถบรรทุก ' . $sequence->index + 1, 'photo' => 'bi-truck', 'plate_number' => sprintf('%06d', $sequence->index + 1)])
            ->create();
    }
}
