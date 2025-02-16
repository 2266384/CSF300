<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Registration;
use App\Models\Source;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                /* Create booleans to check if we have any temp changes - required_if doesn't seem to function
                using an array of data */
                'tlc' => collect($arrayData)->contains(function ($item) {
                    return isset($item['description']) && $item['description'] === 'Temporary - Life changes';
                }),
                'phr' => collect($arrayData)->contains(function ($item) {
                    return isset($item['description']) && $item['description'] === 'Temporary - Post hospital recovery';
                }),
                'yah' => collect($arrayData)->contains(function ($item) {
                    return isset($item['description']) && $item['description'] === 'Temporary - Young adult householder(<18)';
                }),
                'tlc_date' => $request['tlc_date'],
                'phr_date' => $request['phr_date'],
                'yah_date' => $request['yah_date'],
            ];

            // Validation rules
            $rules = [
                'source' => 'required',
                'consent_date' => 'nullable|date|after_or_equal:today',
                'removed_date' => 'nullable|date|after_or_equal:today',
                'recipient_name' => 'required|min:5|max:255',
                'arrayData' => 'required|array|min:1',
                'tlc_date' => 'nullable|required_if:tlc,true|date|after_or_equal:today',
                'phr_date' => 'nullable|required_if:phr,true|date|after_or_equal:today',
                'yah_date' => 'nullable|required_if:yah,true|date|after_or_equal:today',
            ];

            $messages = [
                'source.required' => 'The source is required.',
                'recipient_name.required' => 'Recipient name is required',
                'recipient_name.min' => 'Recipient name must be at least 5 characters',
                'recipient_name.max' => 'Recipient name cannot exceed 255 characters',
                'arrayData.required' => 'The customer must have at least one Need',
                'tlc_date' => 'Temporary - Life changes must have a date',
                'phr_date' => 'Temporary - Post hospital recovery must have a date',
                'yah_date' => 'Temporary - Young adult householder(<18) must have a date',
            ];

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ],422);
            }


            // Access regular data from FormData
            $customer_ref = $request->input('customer_ref');
            $source = $request->input('source');
            $consent_date = $request->input('consent_date');
            $removed_date = $request->input('removed_date');
            $recipient_name = $request->input('recipient_name');
            $tlcDate = $request->input('tlc_date');
            $phrDate = $request->input('phr_date');
            $yahDate = $request->input('yah_date');


            // Create the new Registration and get the ID
            $registration = new Registration();
            $registration->customer = $customer_ref;
            $registration->recipient_name = $recipient_name;
            $registration->source_id = $source;
            $registration->source_type = Source::class;
            $registration->active = true;
            $registration->consent_date = $consent_date;
            $registration->removed_date= $removed_date;
            $registration->save();

            // Get the Customer model and the PSR ID
            $customer = Customer::findorfail($customer_ref);

            $psr_id = $customer->registrations->where('active', 1)
                ->where('removed_date', null)->first()->id;


            /*
             * Save the Needs and Services to the database against the PSR ID
             * Check for updating user so we can assign the right class to it
             */
            // Separate all the needs and services
            $needs = collect($arrayData)->where('type', 'need');
            $services = collect($arrayData)->where('type', 'service');

            // Need update
            if($needs->isNotEmpty()) {

                foreach($needs as $need) {

                    // Set the tempNeedDate
                    if($need['code'] == '32') {
                        $tempEndDate = $tlcDate;
                    } else if ($need['code'] == '33') {
                        $tempEndDate = $phrDate;
                    } else if ($need['code'] == '34') {
                        $tempEndDate = $yahDate;
                    } else {
                        $tempEndDate = null;
                    }

                    $need['psr_id'] = $psr_id;
                    $need['temp_end_date'] = $tempEndDate;

                    addAttribute($need);
                }
            }


            // Service update
            if($services->isNotEmpty()) {

                foreach($services as $data) {

                    $data['psr_id'] = $psr_id;
                    $data['temp_end_date'] = $tempEndDate;

                    addAttribute($data);
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
