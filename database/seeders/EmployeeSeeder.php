<?php

namespace Database\Seeders;

use App\Enums\DepartmentNameId;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Employee::factory()
            ->count(10)
            ->sequence(fn ($sequence) => [
                'department_id' => DepartmentNameId::Deliver(),
                'code' => sprintf('%05d', $sequence->index + 1),
                'photo' => 'bi-person-circle',
                'password' => '1234'
            ])
            ->create();
    }
}
