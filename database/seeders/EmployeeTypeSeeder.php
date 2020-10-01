<?php

namespace Database\Seeders;

use App\Models\EmployeeType;
use Illuminate\Database\Seeder;

class EmployeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmployeeType::updateOrCreate([
            'name' => 'standard'
        ]);

        EmployeeType::updateOrCreate([
            'name' => 'salary'
        ]);

        EmployeeType::updateOrCreate([
            'name' => 'temp'
        ]);
    }
}
