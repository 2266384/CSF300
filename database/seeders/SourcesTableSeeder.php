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
            ['id' => 1, 'source' => 'DCWW', 'active' => true],
            ['id' => 2, 'source' => 'Source 2', 'active' => true],
            ['id' => 3, 'source' => 'Source 3', 'active' => true],
        ];

        foreach ($defaultSources as $source) {
            Source::create($source);
        }
    }
}
