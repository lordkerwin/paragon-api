<?php

namespace Database\Seeders;

use App\Models\ShiftItemType;
use Illuminate\Database\Seeder;

class ShiftItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShiftItemType::updateOrCreate([
            'name' => 'standard'
        ]);

        ShiftItemType::updateOrCreate([
            'name' => 'overtime'
        ]);
    }
}
