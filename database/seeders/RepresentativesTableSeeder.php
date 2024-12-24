<?php

namespace Database\Seeders;

use App\Models\Representative;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RepresentativesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Active representative 1
        $representative = new Representative();
        $representative->name = 'Test Representative';
        $representative->email = 'test@test.com';
        $representative->password = Hash::make('test');
        $representative->organisation = 1;
        $representative->APIKey = encrypt('test');
        $representative->active = true;
        $representative->save();

        // Active representative 2
        $representative = new Representative();
        $representative->name = 'Test Representative2';
        $representative->email = 'test2@test.com';
        $representative->password = Hash::make('test2');
        $representative->organisation = 2;
        $representative->APIKey = encrypt('test2');
        $representative->active = true;
        $representative->save();

        // Inactive representative
        $representative = new Representative();
        $representative->name = 'Test Representative3';
        $representative->email = 'test3@test.com';
        $representative->password = Hash::make('test3');
        $representative->organisation = 3;
        $representative->APIKey = encrypt('test3');
        $representative->active = false;
        $representative->save();
    }
}
