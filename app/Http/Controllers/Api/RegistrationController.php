<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Organisation;
use App\Models\Registration;
use App\Models\Property;
use App\Models\Responsibility;
use App\Models\ThirdPartyRecord;
use App\Models\ThirdPartyUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use function Pest\Laravel\get;

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
            'updateId' => $request['updateId'],
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
            'primary_forename' => 'required|min:1|max:255',
            'primary_surname' => 'required|min:2|max:255',
            'secondary_title' => 'sometimes|string|nullable',
            'secondary_forename' => 'sometimes|min:1|max:255|nullable',
            'secondary_surname' => 'sometimes|min:1|max:255|nullable',
            'recipient_name' => 'required|min:5|max:255',
            'consent_date' => 'nullable|date|after_or_equal:today',
            'needs' => 'required|array|min:1',
            'needs.*.code' => 'required|integer',
            'needs.*.end_date' => 'sometimes|date|nullable',
            'tlc_date' => 'sometimes|nullable|date|after_or_equal:today',
            'phr_date' => 'sometimes|nullable|date|after_or_equal:today',
            'yah_date' => 'sometimes|nullable|date|after_or_equal:today',
        ];

        $messages = [
            'primary_forename.required' => 'Primary forename is required',
            'primary_forename.min' => 'Primary forename must be at least 1 character',
            'primary_forename.max' => 'Primary forename must be less than 255 characters',
            'primary_surname.required' => 'Primary surname is required',
            'primary_surname.min' => 'Primary surname must be at least 2 characters',
            'primary_surname.max' => 'Primary surname must be less than 255 characters',
            'secondary_forename.min' => 'Secondary forename must be at least 1 character',
            'secondary_forename.max' => 'Secondary forename must be less than 255 characters',
            'secondary_surname.min' => 'Secondary surname must be at least 2 characters',
            'secondary_surname.max' => 'Secondary surname must be less than 255 characters',
            'recipient_name.required' => 'Recipient name is required',
            'recipient_name.min' => 'Recipient name must be at least 5 characters',
            'recipient_name.max' => 'Recipient name cannot exceed 255 characters',
            'consent_date.after_or_equal' => 'Consent date must be after or equal to todays date',
            'needs.required' => 'The customer must have at least one Need',
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
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ],422);
        }

    /**
     * Get the user and organisation data for the api call
     */

        $user = Auth::user();

        $organisation = $user->represents;


    /**
     * Add a record of this request to the database table if it wasn't passed by the request
    */
    $thisId = null;

    if (isset($request['updateId'])) {

        $thisId = $request['updateId'];

    } else {

        $thisId = createThirdPartyUpdate($user->id, $request->getMethod(), 0);

    }

    /**
     * Check to see if we have a matching property and throw an error if we don't
     */

        // Extract the property object and convert it into a Request so it can be sent
        // to our helper function
        $propertyRequest = new Request(collect($request->input('property'))->toArray());
        $properties = matchProperties($propertyRequest);

        // Throw an error if we can't match a property
        if($properties->isEmpty()) {

            //dump($propertyRequest);

            createThirdPartyRecord(
                $thisId,
                json_encode($data),
                getNearestProperty($propertyRequest)->id ?? 0,      // Default to 0 if no nearest property returned
                'Property match cannot be found: nearest property provided'
            );

            return response()->json([
                'status' => 422,
                'message' => "Property match cannot be found"
            ]);
        } else {
            $pc = $properties[0]['Postcode'];
        };


    /**
     * Get the occupier details for the matched property and validate the names match
     */
        // Get the occupier details so we can match the names in the POST function

        // Check if the occupier is empty before searching for them
        if(is_null(Property::find($properties->first()['ID'])->customer)) {
            $customerMatch = 'No Match';
        } else {

            $occupierRequest = new Request(Property::find($properties->first()['ID'])->customer->toArray());
            // Add the names from the API request
            $occupierRequest->merge([
                'request_primary_title' => $request->input('primary_title'),
                'request_primary_forename' => $request->input('primary_forename'),
                'request_primary_surname' => $request->input('primary_surname'),
                'request_secondary_title' => $request->input('secondary_title'),
                'request_secondary_forename' => $request->input('secondary_forename'),
                'request_secondary_surname' => $request->input('secondary_surname'),
            ]);

            $customerMatch = matchCustomerNames($occupierRequest);
        }

        if($customerMatch === 'No Match') {

            createThirdPartyRecord(
                $thisId,
                json_encode($data),
                $properties->first()['ID'],
                'Customer match cannot be found'
            );

            return response()->json([
                'status' => 422,
                'message' => "Customer match cannot be found"
            ]);
        }


    /**
     * Check for an existing registration against the matched customer
     */
        // Check if we've already got a registration for the customer
        $registration = Registration::where('customer',$occupierRequest->id)->get();

        // Throw an error if there's already a registration
        if (!$registration->isEmpty()) {

            createThirdPartyRecord(
                $thisId,
                json_encode($data),
                $properties->first()['ID'],
                'Customer already registered'
            );

            return response()->json([
                'status' => 422,
                'message' => "Customer already registered"
            ]);
        }


        // Create the new Registration and get the ID
        $registration = new Registration();
        $registration->customer = $occupierRequest->id;
        $registration->recipient_name = $data['recipient_name'];
        $registration->source_id = $organisation->id;
        $registration->source_type = Organisation::class;
        $registration->active = true;
        $registration->consent_date = $data['consent_date'];
        $registration->save();

        // Get the Customer model and the PSR ID
        $customer = Customer::findorfail($occupierRequest->id);

        $psr_id = $customer->registrations->where('active', 1)
            ->where('removed_date', null)->first()->id;

        // Check if we're currently responsible for the postcode and, if not, add it in
        $postcodes = $organisation->responsible_for->pluck('postcode')->toArray();

        if(!in_array($pc, $postcodes)) {
            $responsibility = new Responsibility();
            $responsibility->organisation = $organisation->id;
            $responsibility->postcode = $pc;
            $responsibility->save();
        }


        /*
        * Save the Needs to the database against the PSR ID
        * Check for updating user so we can assign the right class to it
        */
        $needs = collect($data['needs'])->map(function ($item) use ($psr_id, $data) {

            $item['psr_id'] = $psr_id;
            $item['type'] = 'need';

            if( $item['code'] == 32 ) {
                $item['temp_end_date'] = $data['tlc_date'] ?? null;
            } else if( $item['code'] == 33 ) {
                $item['temp_end_date'] = $data['phr_date'] ?? null;
            } else if( $item['code'] == 34 ) {
                $item['temp_end_date'] = $data['yah_date'] ?? null;
            } else {
                $item['temp_end_date'] = null;
            }

            return $item;
        })->all();

        // Iterate the needs and add them
        foreach($needs as $need) {
            addAttribute($need);
        }

        createThirdPartyRecord(
            $thisId,
            json_encode($data),
            $properties->first()['ID'],
            'Registration created successfully'
        );

        // Response JSON
        return response()->json([
            //'status' => 'success',
            'status' => 200,
            'message' => 'Registration created successfully',
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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

    /**
     * Create multiple registrations
     */
    public function storeAll(Request $request)
    {

        /**
         * Add a record of this request to the database table
         */
        $thisUpdate = createThirdPartyUpdate($request->user()->id, $request->getMethod(), 0);

        $requests = $request->all();
        $controller = app(RegistrationController::class);

        // Iterate the objects in the request and directly call the Controller to create them
        $responses = collect($requests)->map(function ($item) use ($controller, $thisUpdate) {

            // Add the update ID to the new request
            $item['updateId'] = $thisUpdate;
            $internalRequest = new Request($item);

            $response = $controller->store($internalRequest);

            //return $response instanceof JsonResponse ? $response->getData(true) : $response;
            if ($response instanceof JsonResponse) {

                $status = $response->getData(true)['status'];
                $message = $response->getData(true)['message'] ?? 'Unknown error';
                $errors = $response->getData(true)['errors'] ?? [];

                return [
                    'status' => $status,
                    'message' => in_array($status, [200, 201, 204]) ? 'Registration created successfully' : $message,
                    'errors' => $errors,
                ];

                /*
                return [
                    'status' => $response->status(),
                    'message' => $response->getData(true)['message'] ?? 'Unknown error',
                ];
                */
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
