<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Test customers
        // Single Customer No DoB
        Customer::factory()->create([
        'primary_title' => 'Mr.',
        'primary_forename' => 'Test',
        'primary_surname' => 'User1',
        ]);

        // Single Customer with DoB
        Customer::factory()->primaryDoB()->create([
        'primary_title' => 'Mrs.',
        'primary_forename' => 'Test',
        'primary_surname' => 'User2',
        'primary_dob' => '1980-01-01'
        ]);

        // Two Customers No DoBs
        Customer::factory()->secondary()->create([
        'primary_title' => 'Ms.',
        'primary_forename' => 'Test',
        'primary_surname' => 'User3',
        'secondary_title' => 'Mr.',
        'secondary_forename' => 'Test',
        'secondary_surname' => 'User3',
        ]);

        // Two Customers - Primary DoB
        Customer::factory()->primaryDoB()->secondary()->create([
        'primary_title' => 'Miss.',
        'primary_forename' => 'Test',
        'primary_surname' => 'User4',
        'primary_dob' => '1990-01-01',
        'secondary_title' => 'Mr.',
        'secondary_forename' => 'Test',
        'secondary_surname' => 'User4',
        ]);

        // Two Customers - Secondary DoB
        Customer::factory()->secondary()->secondaryDoB()->create([
        'primary_title' => 'Mr.',
        'primary_forename' => 'Test',
        'primary_surname' => 'User5',
        'secondary_title' => 'Capt.',
        'secondary_forename' => 'Test',
        'secondary_surname' => 'User5',
        'secondary_dob' => '1970-06-15',
        ]);

        // Two Customers with DoBs
        Customer::factory()->primaryDoB()->secondary()->secondaryDoB()->create([
        'primary_title' => 'Gen.',
        'primary_forename' => 'Test',
        'primary_surname' => 'User6',
        'primary_dob' => '1950-12-31',
        'secondary_title' => 'Rev.',
        'secondary_forename' => 'Test',
        'secondary_surname' => 'User6',
        'secondary_dob' => '1945-03-02',
        ]);

        // Bulk Customer updates
        // A 20% Single Occupier No DoB
        // B 50% Single Occupier with DoB
        // C 5% Two Customers no DoB
        // D 5% Two Customers Primary DoB
        // E 5% Two Customers Secondary DoB
        // F 15% Two Customers with DoBs

        $total = 1000;
        $A = $total * 0.2;
        $B = $total * 0.5;
        $C = $total * 0.05;
        $D = $total * 0.05;
        $E = $total * 0.05;
        $F = $total * 0.15;

        Customer::factory($A)->create();
        Customer::factory($B)->primaryDob()->create();
        Customer::factory($C)->secondary()->create();
        Customer::factory($D)->primaryDoB()->secondary()->create();
        Customer::factory($E)->secondary()->secondaryDoB()->create();
        Customer::factory($F)->primaryDoB()->secondary()->secondaryDoB()->create();

    }
}