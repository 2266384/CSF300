<?php

namespace Database\Seeders;

use App\Models\Need;
use App\Models\Registration;
use App\Models\Representative;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NeedsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $thisRegistration = 1;
        $thisRepresentative = 1;

        // Need with Representative update
        $need = new Need();
        $need->registration_id = $thisRegistration;
        $need->code = '1';
        $need->temp_end_date = null;
        $need->lastupdate_id = $thisRepresentative;
        $need->lastupdate_type = Representative::class;
        $need->save();

        // Need with User update
        $need = new Need();
        $need->registration_id = $thisRegistration;
        $need->code = '4';
        $need->temp_end_date = null;
        $need->lastupdate_id = 1;
        $need->lastupdate_type = User::class;
        $need->save();


        $thisRegistration = 2;

        // Need with User update
        $need = new Need();
        $need->registration_id = $thisRegistration;
        $need->code = '22';
        $need->temp_end_date = null;
        $need->lastupdate_id = 1;
        $need->lastupdate_type = User::class;
        $need->save();

        // Need with User update
        $need = new Need();
        $need->registration_id = $thisRegistration;
        $need->code = '9';
        $need->temp_end_date = null;
        $need->lastupdate_id = 1;
        $need->lastupdate_type = User::class;
        $need->save();


        $thisRegistration = 3;
        $thisRepresentative = 2;

        // Need with Representative update
        $need = new Need();
        $need->registration_id = $thisRegistration;
        $need->code = '14';
        $need->temp_end_date = null;
        $need->lastupdate_id = $thisRepresentative;
        $need->lastupdate_type = Representative::class;
        $need->save();

        // Need with User update
        $need = new Need();
        $need->registration_id = $thisRegistration;
        $need->code = '4';
        $need->temp_end_date = null;
        $need->lastupdate_id = 26;
        $need->lastupdate_type = User::class;
        $need->save();
    }
}
