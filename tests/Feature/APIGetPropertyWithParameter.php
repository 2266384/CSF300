<?php

use App\Helpers\TestCounter;
use App\Models\Customer;
use App\Models\Need;
use App\Models\Organisation;
use App\Models\Property;
use App\Models\Registration;
use App\Models\Representative;
use App\Models\Responsibility;
use App\Models\Service;
use App\Models\Source;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\NeedCodesTableSeeder;
use Database\Seeders\ServiceCodesTableSeeder;
use Database\Seeders\SourcesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

// Automated test fails due to difference in SQL types between Prod and Test


beforeEach(function () {

    // Clear the database and seed it with test data
    Artisan::call('migrate:fresh');
    (new Tests\Seeders\PestTestSeeder)->run();

});

afterEach(function () {

    (new SourcesTableSeeder())->run();

});



it('authenticate representative can GET Property using parameters from api with bearer token and structure matches', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it authenticate representative can GET Property using parameters from api with bearer token and structure matches', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read']);

    $response = $this->getJson("/api/v1/property?postcode=CF12 3AB");

    dump($response->json());

    $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => [ // `*` means multiple items (array)
                'ID',
                'UPRN',
                'House No',
                'House Name',
                'Street',
                'Town',
                'Parish',
                'County',
                'Postcode'
            ]
        ]);
});

