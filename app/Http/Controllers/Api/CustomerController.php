<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ThirdPartyRecord;
use App\Models\ThirdPartyUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use function Amp\Iterator\toArray;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.   GET
     */
    public function index(Request $request)
    {

        // Get the user making the request so we can return their data only
        $user = $request->user();


        // Get all the properties the organisation is responsible for
        $properties = $user->represents->responsible_for;

        // Get the customer ids in occupancy in the properties
        $customer = $properties->pluck('occupier')->toArray();


        // Get the needs codes for the registered customers with active registrations
        $customers = Customer::whereIn('id', $customer)
            ->whereHas('registrations', function ($query) {
                $query->where('active', 1);
            })
            ->with([
                'needs.description' => function ($query) {
                    $query->select('code', 'description');
                }
            ])
            ->get()
            ->map(function ($customer) use ($properties) {

                // Add in the properties where the customer is the occupier - allows for multiple properties
                $customerProperties = $properties->where('occupier', $customer->id);

                return [
                    'ID' => $customer->id,
                    'Primary Title' => $customer->primary_title,
                    'Primary Forename' => $customer->primary_forename,
                    'Primary Surname' => $customer->primary_surname,
                    'Secondary Title' => $customer->secondary_title,
                    'Secondary Forename' => $customer->secondary_forename,
                    'Secondary Surname' => $customer->secondary_surname,
                    'Properties' => $customerProperties->map(function ($property) {
                        return [
                            'UPRN' => $property->uprn,
                            'House No' => $property->house_number,
                            'House Name' => $property->house_name,
                            'Street' => $property->street,
                            'Town' => $property->town,
                            'Parish' => $property->parish,
                            'County' => $property->county,
                            'Postcode' => $property->postcode,
                        ];
                    }),
                    'Needs' => $customer->needs->map(function ($need) {
                        return [
                            'Code' => $need->description->code,
                            'Description' => $need->description->description,
                            'End Date' => $need->temp_end_date ?? '9999-12-31',   // Default End Date to 31st Dec 9999 if not a temp Need
                        ];
                    }),
                ];
            });


        // Add a record of this request to the database table - we don't need the update ID for this
        createThirdPartyUpdate($user->id, $request->getMethod(), $customers->count());

        return response()->json($customers);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.      GET
     */
    public function show(Request $request, $id)
    {

        $data = [
            'id' => $id,
        ];

        // Validation rules
        $rules = [
            'id' => 'required|integer|exists:customers,id',
        ];

        $messages = [
            'id.required' => 'ID is required',
            'id.integer' => 'ID must be integer',
            'id.exists' => 'ID does not exist',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ]);
        }



        // Get the user making the request so we can return their data only
        $user = $request->user();

        // Get the properties if the Occupier ID matches the provided Customer ID
        $properties = $user->represents->responsible_for->where('occupier', $id);

        /**
         * Add a record of this request to the database table if it wasn't passed by the request
         */
        $thisId = null;

        if (isset($request['updateId'])) {
            $thisId = $request['updateId'];
        } else {
            $thisId = createThirdPartyUpdate($user->id, $request->getMethod(), 0);
        }

        // If we don't have this customer in a property the organisation is responsible for return an error
        if($properties->isEmpty()) {

            createThirdPartyRecord(
                $thisId,
                json_encode($data),
                0,
                'Customer not found'
            );

            return response()->json([
                'status' => 404,
                'message' => 'Customer not found'
            ]);
        }

        // Get the customer and their data
        $customers = Customer::where('id', $id)
            ->whereHas('registrations', function ($query) {
                $query->where('active', 1);
            })
            ->with([
                'needs.description' => function ($query) {
                    $query->select('code', 'description');
                }
            ])
            ->get()
            ->map(function ($customer) use ($properties) {

                // Add in the properties where the customer is the occupier - allows for multiple properties
                $customerProperties = $properties->where('occupier', $customer->id);

                return [
                    'ID' => $customer->id,
                    'Primary Title' => $customer->primary_title,
                    'Primary Forename' => $customer->primary_forename,
                    'Primary Surname' => $customer->primary_surname,
                    'Secondary Title' => $customer->secondary_title,
                    'Secondary Forename' => $customer->secondary_forename,
                    'Secondary Surname' => $customer->secondary_surname,
                    'Properties' => $customerProperties->map(function ($property) {
                        return [
                            'UPRN' => $property->uprn,
                            'House No' => $property->house_number,
                            'House Name' => $property->house_name,
                            'Street' => $property->street,
                            'Town' => $property->town,
                            'Parish' => $property->parish,
                            'County' => $property->county,
                            'Postcode' => $property->postcode,
                        ];
                    }),
                    'Needs' => $customer->needs->map(function ($need) {
                        return [
                            'Code' => $need->description->code,
                            'Description' => $need->description->description,
                            'End Date' => $need->temp_end_date ?? '9999-12-31',   // Default End Date to 31st Dec 9999 if not a temp Need
                        ];
                    }),
                ];
            });

        // Add the update_count to the record
        ThirdPartyUpdate::where('id', $thisId)->update([
            'update_count' => $customers->count(),
        ]);

        return response()->json($customers);


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        // Data to be validated
        $data = [
            'primary_title' => $request['primary_title'],
            'primary_forename' => $request['primary_forename'],
            'primary_surname' => $request['primary_surname'],
            'secondary_title' => $request['secondary_title'],
            'secondary_forename' => $request['secondary_forename'],
            'secondary_surname' => $request['secondary_surname'],
            'recipient_name' => $request['recipient_name'],
            'consent_date' => $request['consent_date'],
            'needs' => $request['needs'],
        ];

        // Extract temp need dates for validation and add them to the data array
        $tlcDate = collect($request->needs)->firstWhere('code', 32);
        $phrDate = collect($request->needs)->firstWhere('code', 33);
        $yahDate = collect($request->needs)->firstWhere('code', 34);

        if($tlcDate) {$data['tlc_date'] = $tlcDate['end_date'];}
        if($phrDate) {$data['phr_date'] = $phrDate['end_date'];}
        if($yahDate) {$data['yah_date'] = $yahDate['end_date'];}

        //return array_map('gettype', $data);
        //return $data;

        // Validation rules
        $rules = [
            'primary_title' => 'sometimes|string|nullable',
            'primary_forename' => 'sometimes|min:1|max:255|nullable',
            'primary_surname' => 'sometimes|min:2|max:255|nullable',
            'secondary_title' => 'sometimes|string|nullable',
            'secondary_forename' => 'sometimes|min:1|max:255|nullable',
            'secondary_surname' => 'sometimes|min:1|max:255|nullable',
            'recipient_name' => 'sometimes|min:5|max:255|nullable',
            'consent_date' => 'nullable|date|after_or_equal:today',
            'needs' => 'sometimes|array|min:1|nullable',
            'needs.*.code' => 'required|integer',
            'needs.*.end_date' => 'sometimes|date|nullable',
            'tlc_date' => 'sometimes|nullable|date|after_or_equal:today',
            'phr_date' => 'sometimes|nullable|date|after_or_equal:today',
            'yah_date' => 'sometimes|nullable|date|after_or_equal:today',
        ];

        $messages = [
            'primary_forename.min' => 'Primary forename must be at least 1 character',
            'primary_forename.max' => 'Primary forename must be less than 255 characters',
            'primary_surname.min' => 'Primary surname must be at least 2 characters',
            'primary_surname.max' => 'Primary surname must be less than 255 characters',
            'secondary_forename.min' => 'Secondary forename must be at least 1 character',
            'secondary_forename.max' => 'Secondary forename must be less than 255 characters',
            'secondary_surname.min' => 'Secondary surname must be at least 2 characters',
            'secondary_surname.max' => 'Secondary surname must be less than 255 characters',
            'recipient_name.min' => 'Recipient name must be at least 5 characters',
            'recipient_name.max' => 'Recipient name cannot exceed 255 characters',
            'consent_date.after_or_equal' => 'Consent date must be after or equal to todays date',
            'needs.min' => 'Customer must have at least one need',
            'needs.*.code.integer' => 'Need code must be an integer',
            'tlc_date' => 'Temporary - Life changes must have a date',
            'tlc_date.after_or_equal' => 'Temporary - Life changes date must be after or equal to todays date',
            'phr_date' => 'Temporary - Post hospital recovery must have a date',
            'phr_date.after_or_equal' => 'Temporary - Post hospital recovery date must be after or equal to todays date',
            'yah_date' => 'Temporary - Young adult householder(<18) must have a date',
            'yah_date.after_or_equal' => 'Temporary - Young adult householder(<18) date must be after or equal to todays date',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ]);
        }

        /**
         * Add a record of this request to the database table if it wasn't passed by the request
         */
        $thisId = null;

        if (isset($request['updateId'])) {
            $thisId = $request['updateId'];
        } else {
            $thisId = createThirdPartyUpdate($request->user()->id, $request->getMethod(), 0);
        }

        // Get the customer we're updating
        $customer = Customer::find($id);
/*
        // Remove any blank fields and update them
        $customerUpdate = array_filter($request->only([
            'primary_title',
            'primary_forename',
            'primary_surname',
            'secondary_title',
            'secondary_forename',
            'secondary_surname',
        ]), function ($item) { return !is_null($item); });
        $customer->update($customerUpdate);
*/

        //return response()->json($customer);
        // Update the registration data
        $registration = $customer->registrations->first();

        $registrationUpdate = array_filter($request->only([
            'recipient_name',
            'consent_date',
        ]), function ($item) { return !is_null($item); });


        if (is_null($registration)) {

            createThirdPartyRecord(
                $thisId,
                json_encode($data),
                0,
                'Customer does not have a live registration'
            );

            return response()->json([
                'status' => 422,
                'message' => 'Customer does not have a live registration',
            ]);
/*
        } else {
            $registration->update($customerUpdate);
*/
        }

        // Update the Needs
        $currentNeeds = $registration->needs->where('active', 1);

        //return response()->json($request['needs']);

        $needCodesUpdate = array_column($data['needs'], 'code');
        $currentNeedCodes = array_column($currentNeeds->toArray(), 'code');


        // Identify the Needs to be added
        $addNeeds = collect($data['needs'])->filter(function ($item) use ($currentNeedCodes) {
            return !in_array($item['code'], $currentNeedCodes);
        })->values()->toArray();


        if ( !blank($addNeeds) ) {
            foreach ($addNeeds as $addNeed) {

                /* Set the temp end date */
                if(in_array($addNeed['code'], ['32', '33','34'])) {
                    $tempEndDate = $addNeed['end_date'];
                } else {
                    $tempEndDate = null;
                }

                // Add ID and Update Type to array
                $addNeed['type'] = 'need';
                $addNeed['psr_id'] = $registration->id;
                $addNeed['temp_end_date'] = $tempEndDate;

                addAttribute($addNeed);
            }
        }

        createThirdPartyRecord(
            $thisId,
            json_encode($data),
            0,
            'Customer updated successfully'
        );

        // Response JSON
        return response()->json([
            'status' => 200,
            'message' => 'Customer updated successfully',
        ],200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Create multiple registrations
     */
    public function updateAll(Request $request)
    {

        // Get the request data
        $requests = $request->all();
        $customerController = app(CustomerController::class);

        /**
         * Add a record of this request to the database table
         */
        $thisUpdate = createThirdPartyUpdate($request->user()->id, $request->getMethod(), 0);

        // Iterate the requests and call the controller directly for each
        $responses = collect($requests)->map(function ($item) use ($customerController, $thisUpdate) {

            // Add the update ID to the new request
            $item['updateId'] = $thisUpdate;
            $internalRequest = new Request($item);

            $response = $customerController->update($internalRequest, $item['id']);

            //return $response instanceof JsonResponse ? $response->getData(true) : $response;
            if ($response instanceof JsonResponse) {
                $status = $response->getData(true)['status'];
                $message = $response->getData(true)['message'] ?? 'Unknown error';
                $errors = $response->getData(true)['errors'] ?? [];

                return [
                    'status' => $status,
                    'message' => in_array($status, [200, 201, 204]) ? 'Customer updated successfully' : $message,
                    'errors' => $errors,
                ];
            }
        });

        $hasFailure = $responses->contains(fn($item) => isset($item['status']) && !in_array($item['status'], [200, 201, 204]));

        // Return the responses
        return response()->json([
            'status' => $hasFailure ? 'failed' : 'success',
            'submitted' => $responses
        ]);
    }

}
