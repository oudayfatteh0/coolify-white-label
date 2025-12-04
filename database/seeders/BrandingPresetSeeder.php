<?php

namespace Database\Seeders;

use App\Models\BrandingPreset;
use Illuminate\Database\Seeder;

class BrandingPresetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BrandingPreset::seedSystemPresets();
    }
}
