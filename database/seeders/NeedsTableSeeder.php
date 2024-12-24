<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Need;
use App\Models\Representative;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NeedsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Need with Representative update
        $need = new Need();
        $need->customer = Customer::all()->random()->id;
        $need->code = 1;
        $need->lastupdate_id = 2;
        $need->lastupdate_type = Representative::class;
        $need->save();

        // Need with User update
        $need = new Need();
        $need->customer = Customer::all()->random()->id;
        $need->code = 4;
        $need->lastupdate_id = 1;
        $need->lastupdate_type = User::class;
        $need->save();
    }
}
