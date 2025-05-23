<?php

namespace Database\Seeders;

use App\Models\Registration;
use App\Models\Service;
use App\Models\Representative;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Service with Representative update
        $service = new Service();
        $service->registration_id = 2;
        $service->code = '16P';
        $service->temp_end_date = null;
        $service->lastupdate_id = 2;
        $service->lastupdate_type = Representative::class;
        $service->save();

        // Service with User update
        $service = new Service();
        $service->registration_id = 1;
        $service->code = 'PAS';
        $service->temp_end_date = null;
        $service->lastupdate_id = 1;
        $service->lastupdate_type = User::class;
        $service->save();
    }
}
