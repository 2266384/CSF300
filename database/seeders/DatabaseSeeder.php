<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([UsersTableSeeder::class]);
        $this->call([SourcesTableSeeder::class]);
        $this->call([CustomersTableSeeder::class]);
        $this->call([TelephonesTableSeeder::class]);
        $this->call([EmailsTableSeeder::class]);
        $this->call([NeedCodesTableSeeder::class]);
        $this->call([ServiceCodesTableSeeder::class]);
        $this->call([PropertiesTableSeeder::class]);
        $this->call([OrganisationsTableSeeder::class]);
        $this->call([ResponsibilitiesTableSeeder::class]);
        $this->call([RepresentativesTableSeeder::class]);
        $this->call([NeedsTableSeeder::class]);
        $this->call([ServicesTableSeeder::class]);
        $this->call([ActionsTableSeeder::class]);

    }
}
