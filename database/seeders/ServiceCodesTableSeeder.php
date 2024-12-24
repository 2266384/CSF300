<?php

namespace Database\Seeders;

use App\Models\ServiceCode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultServices = [
            ['code' => 'FBR', 'description' => 'Braille bill', 'active' => true],
            ['code' => 'FCD', 'description' => 'Bill on CD', 'active' => true],
            ['code' => 'FMP', 'description' => 'Bill on MP3', 'active' => true],
            ['code' => 'FPH', 'description' => 'Read Bill over phone', 'active' => true],
            ['code' => '16P', 'description' => 'Large Print 16pt', 'active' => true],
            ['code' => '20P', 'description' => 'Large Print 20pt', 'active' => true],
            ['code' => '24P', 'description' => 'Large Print 24pt', 'active' => true],
            ['code' => 'CLB', 'description' => 'Print Light Blue', 'active' => true],
            ['code' => 'CDB', 'description' => 'Print Dark Blue', 'active' => true],
            ['code' => 'CRE', 'description' => 'Print Red', 'active' => true],
            ['code' => 'CYE', 'description' => 'Print Yellow', 'active' => true],
            ['code' => 'TXT', 'description' => 'Text Relay', 'active' => true],
            ['code' => 'AMR', 'description' => 'Additional Meter Reads', 'active' => false],
            ['code' => 'NOM', 'description' => 'Customer Nominee', 'active' => true],
            ['code' => 'PAS', 'description' => 'Password Request', 'active' => true]
        ];

        // Note - using INSERT does not generate created_at and updated_at timestamps
        ServiceCode::insert($defaultServices);
    }
}
