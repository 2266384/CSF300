<?php

use App\Models\Customer;
use App\Models\Need;
use App\Models\Service;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

/**
 * Concatenates Primary and Secondary names
 */
if (! function_exists('customerNames')) {
    function customerNames(Customer $customer)
    {

        $customerNames = array_filter([
            [
                'Title' => $customer->primary_title,
                'Forename' => $customer->primary_forename,
                'Surname' =>$customer->primary_surname
            ],
            [
                'Title' => $customer->secondary_title,
                'Forename' => $customer->secondary_forename,
                'Surname' => $customer->secondary_surname
            ]
        ]);

        if(count($customerNames) === 2){
            return implode(' & ', $customerNames);
        }

        return implode(' ',$customerNames);
    }

}

if (! function_exists('addAttribute')) {

    function addAttribute(array $data)
    {

        $userId = Auth::id();
        $updateType = Auth::user()::class;

        if ($data['type'] == 'need') {

            // Check to see if we have a historic code for this and if not create a new one
            try {
                $id = Need::where('registration_id', $data['psr_id'])
                    ->where('code', $data['code'])
                    ->where('active', 0)
                    ->firstOrFail()
                    ->id;

                $need = Need::find($id);
                $need->active = 1;
                $need->temp_end_date = $data['temp_end_date'];
                $need->save();
            } catch (ModelNotFoundException $e) {


                Need::create([
                    'registration_id' => $data['psr_id'],
                    'code' => $data['code'],
                    'temp_end_date' => $data['temp_end_date'],
                    'lastupdate_id' => $userId,
                    'lastupdate_type' => $updateType,
                ]);

            }

        } else if ($data['type'] == 'service') {

            // Check to see if we have a historic code for this and if not create a new one
            try {
                $id = Service::where('registration_id', $data['psr_id'])
                    ->where('code', $data['code'])
                    ->where('active', 0)
                    ->firstOrFail()
                    ->id;

                $service = Service::find($id);
                $service->active = 1;
            } catch (ModelNotFoundException $e) {

                Service::create([
                    'registration_id' => $data['psr_id'],
                    'code' => $data['code'],
                    'lastupdate_id' => $userId,
                    'lastupdate_type' => $updateType,
                ]);

            }
        }
    }

}


if (! function_exists('removeAttribute')) {
    function removeAttribute(Array $data) {

        if ( $data['type'] == 'need' ) {

            Need::findorfail($data['id'])
                ->update(['active' => 0]);

        } else if ( $data['type'] == 'service' ) {

            Service::findorfail($data['id'])
                ->update(['active' => 0]);

        }

    }
}
