<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Property;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // House Number with Letter and with Occupier
        $property = new Property();
        $property->uprn = 1234567890;
        $property->house_number = '14A';
        $property->street = 'The Road';
        $property->town = 'The Town';
        $property->postcode = 'CF12 3AB';
        $property->occupier = Customer::all()->random()->id;
        $property->save();

        // House Number without Letter, without Town, with Occupier
        $property = new Property();
        $property->uprn = 9876543210;
        $property->house_number = '7';
        $property->street = 'High Street';
        $property->postcode = 'NP8 1ZU';
        $property->occupier = Customer::all()->random()->id;
        $property->save();

        // House Number and Name, with Town, with Occupier
        $property = new Property();
        $property->uprn = 1122334455;
        $property->house_number = '1';
        $property->house_name = 'White House';
        $property->street = 'Long Lane';
        $property->postcode = 'AB12 9DJ';
        $property->occupier = Customer::all()->random()->id;
        $property->save();

        // House Number and no Occupier
        $property = new Property();
        $property->uprn = 2233445566;
        $property->house_number = '99';
        $property->street = 'The Avenue';
        $property->postcode = 'AB1 2GZ';
        $property->save();

        Property::factory()->importcsv();

    }
}
