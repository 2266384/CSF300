<?php

namespace Database\Seeders;

use App\Models\NeedCode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NeedCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultNeeds = [
            ['code' => '1', 'description' => 'Nebuliser and Apnoea Monitor', 'active' => true],
            ['code' => '2', 'description' => 'Heart, Lung, and Ventilator', 'active' => true],
            ['code' => '3', 'description' => 'Dialysis, Feeding Pump, and Automated Medication', 'active' => true],
            ['code' => '4', 'description' => 'Oxygen Concentrator', 'active' => true],
            ['code' => '8', 'description' => 'Blind', 'active' => true],
            ['code' => '9', 'description' => 'Partially Sighted', 'active' => true],
            ['code' => '12', 'description' => 'Stair Lift, Hoist, Electric Bed', 'active' => true],
            ['code' => '14', 'description' => 'Pensionable Age', 'active' => true],
            ['code' => '15', 'description' => 'Physical Impairment', 'active' => true],
            ['code' => '17', 'description' => 'Unable to communicate in English', 'active' => true],
            ['code' => '18', 'description' => 'Developmental Condition', 'active' => true],
            ['code' => '19', 'description' => 'Unable to answer door', 'active' => true],
            ['code' => '20', 'description' => 'Dementia(s)/Cognitive Impairment', 'active' => true],
            ['code' => '22', 'description' => 'Chronic/Serious Illness', 'active' => true],
            ['code' => '23', 'description' => 'Medically dependent showering/bathing', 'active' => true],
            ['code' => '24', 'description' => 'Careline/Telecare system', 'active' => true],
            ['code' => '25', 'description' => 'Medicine Refrigeration', 'active' => true],
            ['code' => '26', 'description' => 'Oxygen Use', 'active' => true],
            ['code' => '27', 'description' => 'Poor sense of smell/taste', 'active' => true],
            ['code' => '28', 'description' => 'Restricted hand movement', 'active' => true],
            ['code' => '29', 'description' => 'Families with young children (12 months or under)', 'active' => true],
            ['code' => '30', 'description' => 'Mental health', 'active' => true],
            ['code' => '31', 'description' => 'Additional presence preferred', 'active' => false],
            ['code' => '32', 'description' => 'Temporary - Life changes', 'active' => true],
            ['code' => '33', 'description' => 'Temporary - Post hospital recovery', 'active' => true],
            ['code' => '34', 'description' => 'Temporary - Young adult householder(<18)', 'active' => true],
            ['code' => '35', 'description' => 'Hearing Impairment (inc. Deaf)', 'active' => true],
            ['code' => '36', 'description' => 'Speech Impairment', 'active' => true],
            ['code' => '37', 'description' => 'Water Dependent', 'active' => true],
        ];

        // Note - using INSERT does not generate created_at and updated_at timestamps
        NeedCode::insert($defaultNeeds);

    }
}
