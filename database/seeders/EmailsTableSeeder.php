<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Email;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = new Email();
        $email->customer_id = Customer::all()->random()->id;
        $email->address = 'abc@123.com';
        $email->save();

        $email = new Email();
        $email->customer_id = Customer::all()->random()->id;
        $email->address = 'bob@robert.co.uk';
        $email->default = true;
        $email->save();

        $email = new Email();
        $email->customer_id = Customer::all()->random()->id;
        $email->address = 'info@xyz.org';
        $email->save();

        /**
         * Loop to create multiple emails in the database
         * Create the emails one at a time, check for the existence of a
         * default flag and set any subsequent emails for false
         */

        for($x = 0; $x < 100; $x++){

            // Get a random customer id
            $customer = Customer::inRandomOrder()->first()->id;

            // Check if the customer already has a default email
            $hasDefault = Email::where('customer_id', $customer)
                                    ->where('default', true)
                                    ->exists();

            Email::factory()->create([
                'customer_id' => $customer,
                'default' => !$hasDefault,
            ]);
        }

    }
}
