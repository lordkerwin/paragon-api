<?php

namespace Database\Seeders;

use App\Models\EmployeeType;
use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EventType::updateOrCreate([
            'name' => 'clock-in'
        ]);

        EventType::updateOrCreate([
            'name' => 'clock-out'
        ]);

        EventType::updateOrCreate([
            'name' => 'on-site'
        ]);

        EventType::updateOrCreate([
            'name' => 'off-site'
        ]);
    }
}
