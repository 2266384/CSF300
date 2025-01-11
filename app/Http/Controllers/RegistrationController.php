<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Need;
use App\Models\Registration;
use App\Models\Service;
use App\Models\Source;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Customer $customer)
    {
        return view('registrations.create', ['customer' => $customer]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {

            // Decode the array data (sent as JSON string)
            $arrayData = json_decode($request->input('arrayData'), true); // Decode the JSON string into an array

            // Check the decode was successful
            if(json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON');
            }

            // Data to be validated
            $data = [
                'source' => $request['source'],
                'consent_date' => $request['consent_date'],
                'removed_date' => $request['removed_date'],
                'recipient_name' => $request['recipient_name'],
                'arrayData' => $arrayData,
            ];

            // Validation rules
            $rules = [
                'source' => 'required',
                'consent_date' => 'nullable|date|after_or_equal:today',
                'removed_date' => 'nullable|date|after_or_equal:today',
                'recipient_name' => 'required|max:255',
                'arrayData' => 'required|array|min:1',
            ];

            $validator = Validator::make($data, $rules);

            if($validator->fails()) {
                throw new \Exception('Validation failed');
            }


            // Access regular data from FormData
            //$sap_bp_ref = $request->input('sap_bp_ref');
            $customer_ref = $request->input('customer_ref');
            $source = $request->input('source');
            $consent_date = $request->input('consent_date');
            $removed_date = $request->input('removed_date');
            $recipient_name = $request->input('recipient_name');


            // Create the new Registration and get the ID
            $registration = new Registration();
            $registration->customer = $customer_ref;
            $registration->recipient_name = $recipient_name;
            $registration->source = Source::find($source)->id;
            $registration->active = true;
            $registration->consent_date = $consent_date;
            $registration->removed_date= $removed_date;
            $registration->save();

            // Get the Customer model and the PSR ID
            $customer = Customer::findorfail($customer_ref);

            $psr_id = $customer->registrations->where('active', '=', 1)
                ->where('removed_date', '=', null)->first()->id;


            /*
             * Save the Needs and Services to the database against the PSR ID
             * Check for updating user so we can assign the right class to it
             */

            $updateType = User::class;

            // Separate all the needs and services
            $needs = collect($arrayData)->where('type', 'need');
            $services = collect($arrayData)->where('type', 'service');



            // Need update
            if($needs->isNotEmpty()) {

                foreach($needs as $need) {
                    Need::create([
                        'registration_id' => $psr_id,
                        'code' => $need['code'],
                        'lastupdate_id' => 1,
                        'lastupdate_type' => $updateType,
                    ]);
                }
            }


            // Service update
            if($services->isNotEmpty()) {

                foreach($services as $data) {
                    Service::create([
                        'registration_id' => $psr_id,
                        'code' => $data['code'],
                        'lastupdate_id' => 1,
                        'lastupdate_type' => $updateType,
                    ]);
                }
            }


            // Response JSON
            return response()->json([
                'success' => true,
                'message' => 'Registration created successfully',
                'redirect_url' => route('customers.show', ['customer' => $customer->id])
            ]);

            /*
            return response()->json([
                'message' => 'successfully retrieved data',
                'data' => [
                    //'sap_bp_ref' => $sap_bp_ref,
                    'customer_ref' => $customer_ref,
                    'source' => $source,
                    'consent_date' => $consent_date,
                    'removed_date' => $removed_date,
                    'recipient_name' => $recipient_name,
                    'arrayData' => $arrayData,
                    'needs' => $needs,
                    'services' => $services,
                    //'psr_id' => $psr_id,
                ]
            ]);
            */


        } catch (\Exception $e) {
            // Catch any exceptions and return an error response
            return response()->json([
                'error' => 'An error occurred while processing the request.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
