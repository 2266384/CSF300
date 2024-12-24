<?php

namespace Database\Seeders;

use App\Models\Organisation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganisationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $organisation = new Organisation();
        $organisation->name = 'Test Organisation';
        $organisation->active = true;
        $organisation->save();

        $organisation = new Organisation();
        $organisation->name = 'Test Organisation 2';
        $organisation->active = true;
        $organisation->save();

        $organisation = new Organisation();
        $organisation->name = 'Test Organisation 3';
        $organisation->active = false;
        $organisation->save();
    }
}
