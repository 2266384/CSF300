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
        $telephone->customer_id = Customer::inRandomOrder()->first()->id;
        $telephone->std = '00000';
        $telephone->number = '000000';
        $telephone->type = 'Work';
        $telephone->default = false;
        $telephone->save();

        $telephone = new Telephone();
        $telephone->customer_id = Customer::inRandomOrder()->first()->id;
        $telephone->std = '01234';
        $telephone->number = '567890';
        $telephone->type = 'Home';
        $telephone->default = false;
        $telephone->save();

        $telephone = new Telephone();
        $telephone->customer_id = Customer::inRandomOrder()->first()->id;
        $telephone->std = '07777';
        $telephone->number = '777777';
        $telephone->type = 'Mobile';
        $telephone->default = true;
        $telephone->save();


        // Telephone numbers default to Mobile
        for($x = 0; $x < 100; $x++) {

            $customer = Customer::inRandomOrder()->first();

            // Check if the customer already has a default telephone
            $hasDefault = Telephone::where('customer_id', $customer->id)
                ->where('default', true)
                ->exists();

            // Create new telephone with string STD
            Telephone::factory()->state([
                'customer_id' => $customer->id,
                'default' => $hasDefault ? false : true,
                'std' => '07' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT)
            ])->create();
        }

        // Create some Landlines too
        for($x = 0; $x < 100; $x++) {

            $customer = Customer::inRandomOrder()->first();

            // Check if the customer already has a default telephone
            $hasDefault = Telephone::where('customer_id', $customer->id)
                ->where('default', true)
                ->exists();

            // Create landline with string STD
            Telephone::factory()->landline()->state([
                'customer_id' => $customer->id,
                'default' => $hasDefault ? false : true,
                'std' => '01' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT)
            ])->create();
        }


    }
}
