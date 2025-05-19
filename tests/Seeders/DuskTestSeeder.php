<?php

namespace Tests\Seeders;

use App\Models\Customer;
use App\Models\Need;
use App\Models\Organisation;
use App\Models\Property;
use App\Models\Registration;
use App\Models\Representative;
use App\Models\Responsibility;
use App\Models\Service;
use App\Models\ServiceCode;
use App\Models\Source;
use Database\Seeders\NeedCodesTableSeeder;
use Database\Seeders\ServiceCodesTableSeeder;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DuskTestSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'test@example.com',
            'password' => 'adminpassword',
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => 'userpassword',
            'is_admin' => false,
        ]);

        (new NeedCodesTableSeeder())->run();
        (new ServiceCodesTableSeeder())->run();

        Organisation::insert([
            'id' => 1,
            'name' => 'Test Organisation',
            'active' => true
        ]);

        Organisation::insert([
            'id' => 2,
            'name' => 'Test Organisation 2',
            'active' => true
        ]);

        Representative::insert([
                'id' => 1,
                'name' => 'Test Representative',
                'email' => 'test@test.com',
                'password' => Hash::make('password'),
                'organisation_id' => 1,
                'active' => true]
        );

        Customer::insert([
            'id' => 1,
            'SAP_reference' => 9876543210,
            'primary_title' => 'Mr.',
            'primary_forename' => 'Forename',
            'primary_surname' => 'Surname',
            'secondary_title' => 'Mrs.',
            'secondary_forename' => 'Forename2',
            'secondary_surname' => 'Surname2',
        ]);

        Customer::insert([
            'id' => 2,
            'SAP_reference' => 9123456789,
            'primary_title' => 'Mr.',
            'primary_forename' => 'Forename3',
            'primary_surname' => 'Surname3',
            'secondary_title' => '',
            'secondary_forename' => '',
            'secondary_surname' => '',
        ]);

        Property::insert([
            'id' => 1,
            'uprn' => 1234567890,
            'house_number' => '14A',
            'street' => 'Street Address',
            'town' => 'Town',
            'postcode' => 'CF12 3AB',
            'occupier' => 1
        ]);

        Property::insert([
            'id' => 2,
            'uprn' => 1023456789,
            'house_number' => '6',
            'house_name' => 'The House',
            'street' => 'Broad Street',
            'town' => 'MyTown',
            'postcode' => 'NP11 3AB',
            'occupier' => 2
        ]);

        Property::insert([
            'id' => 3,
            'uprn' => 1234056789,
            'house_number' => '34B',
            'house_name' => '',
            'street' => 'Long Lane',
            'town' => 'MyTown',
            'postcode' => 'SA7 6TY',
        ]);

        Registration::insert([
            'id' => 1,
            'customer' => 1,
            'recipient_name' => 'Recipient Name',
            'source_id' => 1,
            'source_type' => Source::class,
            'consent_date' => '2025-06-11',
            'active' => true,
        ]);

        Registration::insert([
            'id' => 2,
            'customer' => 2,
            'recipient_name' => 'Recipient Name 2',
            'source_id' => 1,
            'source_type' => Organisation::class,
            'active' => true,
        ]);

        Responsibility::insert([
            'id' => 1,
            'organisation' => 1,
            'postcode' => 'CF12 3AB'
        ]);

        Responsibility::insert([
            'id' => 2,
            'organisation' => 1,
            'postcode' => 'NP11 3AB'
        ]);

        Need::insert([
            'id' => 1,
            'registration_id' => 1,
            'code' => 1,
            'lastupdate_id' => 1,
            'lastupdate_type' => Representative::class,
        ]);

        Need::insert([
            'id' => 2,
            'registration_id' => 1,
            'code' => 9,
            'lastupdate_id' => 1,
            'lastupdate_type' => Representative::class,
        ]);

        Need::insert([
            'id' => 3,
            'registration_id' => 1,
            'code' => 32,
            'temp_end_date' => '2025-06-11',
            'lastupdate_id' => 1,
            'lastupdate_type' => Representative::class,
        ]);

        Service::insert([
            'id' => 1,
            'registration_id' => 1,
            'code' => '16P',
            'lastupdate_id' => 1,
            'lastupdate_type' => Representative::class,
        ]);



    }

}
