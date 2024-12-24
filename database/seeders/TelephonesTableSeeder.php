<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Telephone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TelephonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $telephone = new Telephone();
        $telephone->customer_id = Customer::all()->random()->id;
        $telephone->std = '00000';
        $telephone->number = '000000';
        $telephone->type = 'Work';
        $telephone->save();

        $telephone = new Telephone();
        $telephone->customer_id = Customer::all()->random()->id;
        $telephone->std = '01234';
        $telephone->number = '567890';
        $telephone->type = 'Home';
        $telephone->save();

        $telephone = new Telephone();
        $telephone->customer_id = Customer::all()->random()->id;
        $telephone->std = '07777';
        $telephone->number = '777777';
        $telephone->type = 'Mobile';
        $telephone->default = true;
        $telephone->save();


        // Telephone numbers default to Mobile
        for($x = 0; $x < 100; $x++) {

            // Get a random customer id
            $customer = Customer::inRandomOrder()->first()->id;

            // Check if the customer already has a default email
            $hasDefault = Telephone::where('customer_id', $customer)
                ->where('default', true)
                ->exists();

            Telephone::factory()->create([
                'customer_id' => $customer,
                'default' => !$hasDefault,
            ]);
        }

        // Create some Landlines too
        for($x = 0; $x < 100; $x++) {

            // Get a random customer id
            $customer = Customer::inRandomOrder()->first()->id;

            // Check if the customer already has a default email
            $hasDefault = Telephone::where('customer_id', $customer)
                ->where('default', true)
                ->exists();

            // Telephone numbers default to Mobile
            Telephone::factory()->landline()->create([
                'customer_id' => $customer,
                'default' => !$hasDefault,
            ]);
        }


    }
}
