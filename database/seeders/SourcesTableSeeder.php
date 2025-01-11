<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSources = [
            ['source' => 'DCWW', 'active' => true],
            ['source' => 'Source 2', 'active' => true],
            ['source' => 'Source 3', 'active' => true],
        ];

        // Note - using INSERT does not generate created_at and updated_at timestamps
        Source::insert($defaultSources);
    }
}
