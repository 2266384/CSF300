<?php

namespace Database\Seeders;

use App\Models\Responsibility;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResponsibilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $responsibility = new Responsibility();
        $responsibility->organisation = 1;
        $responsibility->postcode = 'CF12 3AB';
        $responsibility->save();

        $responsibility = new Responsibility();
        $responsibility->organisation = 1;
        $responsibility->postcode = 'NP8 1ZU';
        $responsibility->save();

        $responsibility = new Responsibility();
        $responsibility->organisation = 2;
        $responsibility->postcode = 'AB1 2GZ';
        $responsibility->save();
    }
}
