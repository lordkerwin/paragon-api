<?php

namespace Database\Seeders;

use App\Models\ShiftStatus;
use Illuminate\Database\Seeder;

class ShiftStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShiftStatus::updateOrCreate([
            'name' => 'new'
        ]);

        ShiftStatus::updateOrCreate([
            'name' => 'queued-processing'
        ]);

        ShiftStatus::updateOrCreate([
            'name' => 'processing'
        ]);

        ShiftStatus::updateOrCreate([
            'name' => 'error-processing'
        ]);

        ShiftStatus::updateOrCreate([
            'name' => 'processed'
        ]);


    }
}
