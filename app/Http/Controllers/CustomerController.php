<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Need;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Customers are not created in the Priority Service Register directly but
 * are obtained from the legacy system which would need to be via API.
 *
 * Priority Service Register creates Registrations which are handled in
 * the RegistrationController.  All other functionality such as viewing
 * or updating records are managed in this controller as they are all
 * related to Customer ID
 */

class CustomerController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('customers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //dd($customer);
        return view('customers.show', ['customer' => $customer]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', ['customer' => $customer]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        try {


            // Decode the array data (sent as JSON string)
            $arrayData = json_decode($request->input('arrayData'), true); // Decode the JSON string into an array

            // Check the decode was successful
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON');
            }

            // Data to be validated
            $data = [
                'consent_date' => $request['consent_date'],
                'removed_date' => $request['removed_date'],
                'recipient_name' => $request['recipient_name'],
                'psr_id' => $request['psr_id'],
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
                'consent_date' => 'nullable|date|after_or_equal:today',
                'remove_dated' => 'nullable|date|after_or_equal:today',
                'recipient_name' => 'required|min:5|max:255',
                'psr_id' => 'required|numeric',
                'arrayData' => 'required|array|min:1',
                'tlc_date' => 'nullable|required_if:tlc,true|date|after_or_equal:today',
                'phr_date' => 'nullable|required_if:phr,true|date|after_or_equal:today',
                'yah_date' => 'nullable|required_if:yah,true|date|after_or_equal:today',
            ];

            $messages = [
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
            $consent_date = $request->input('consent_date');
            $removed_date = $request->input('removed_date');
            $recipient_name = $request->input('recipient_name');
            $psr_id = $request->input('psr_id');
            $tlcDate = $request->input('tlc_date');
            $phrDate = $request->input('phr_date');
            $yahDate = $request->input('yah_date');


            // Get the registration and customer associated with the PSR ID
            $registration = Registration::findorfail($psr_id);
            $customer = Customer::findorfail($customer_ref);

            //dd($registration, $customer);

            /*
             * Check if we need to update any of the fields and apply the
             * change to the registration details
             */
            if ($consent_date != $registration->consent_date ||
                $removed_date != $registration->removed_date ||
                $recipient_name != $registration->recipient_name) {

                $update = [
                    'consent_date' => $consent_date,
                    'removed_date' => $removed_date,
                    'recipient_name' => $recipient_name,
                    'active' => 0,
                ];

                $registration->update($update);

            }


            /*
             * Save the Needs and Services to the database against the PSR ID
             * Check for updating user so we can assign the right class to it
             */
            // Current Need and Service Data
            $currentNeeds = $registration->needs->where('active', 1);
            $currentServices = $registration->services->where('active', 1);

            // Set the type for the current Needs and Services
            foreach ($currentNeeds as $need) {
                $need['type'] = 'need';
            }

            foreach ($currentServices as $service) {
                $service['type'] = 'service';
            }

            $attributeCodes = array_column($arrayData, 'code');
            $currentAttributes = array_merge($currentNeeds->toArray(), $currentServices->toArray());
            $currentAttributeCodes = array_column($currentAttributes, 'code');

            /*
            * Check if the Removed Date is not blank so we can end all the
            * Needs and Services applied to the Registrant
            */
            if(!is_null($removed_date)) {

                foreach ($currentAttributes as $currentAttribute) {
                    removeAttribute($currentAttribute);
                }

            } else {

                // Identify the Needs to be added and removed
                $addAttributes = collect($arrayData)->filter(function ($item) use ($currentAttributeCodes) {
                    return !in_array($item['code'], $currentAttributeCodes);
                })->values()->toArray();

                $removeAttributes = collect($currentAttributes)->filter(function ($item) use ($attributeCodes) {
                    return !in_array($item['code'], $attributeCodes);
                })->values()->toArray();


                if ( !blank($removeAttributes) ) {
                    foreach ($removeAttributes as $removeAttribute) {
                        removeAttribute($removeAttribute);
                    }
                }

                if ( !blank($addAttributes) ) {
                    foreach ($addAttributes as $addAttribute) {

                        // Set the tempNeedDate
                        if($addAttribute['code'] == '32') {
                            $tempEndDate = $tlcDate;
                        } else if ($addAttribute['code'] == '33') {
                            $tempEndDate = $phrDate;
                        } else if ($addAttribute['code'] == '34') {
                            $tempEndDate = $yahDate;
                        } else {
                            $tempEndDate = null;
                        }

                        // Add ID and Update Type to array
                        $addAttribute['psr_id'] = $psr_id;
                        //$addAttribute['updateType'] = $updateType;
                        $addAttribute['temp_end_date'] = $tempEndDate;

                        addAttribute($addAttribute);
                    }
                }
            }

            // Identify the temp Needs with dates to update
            // Get all the customer temp needs in a table
            $tempNeeds = [
                'tlc' => $customer->needs->where('active', 1)
                    ->where('code','32')
                    ->first(),
                'phr' => $customer->needs->where('active', 1)
                    ->where('code','33')
                    ->first(),
                'yah' => $customer->needs->where('active', 1)
                    ->where('code','34')
                    ->first(),
            ];

            // Iterate through the list and update the temp end dates
            foreach ($tempNeeds as $key => $need) {
                // Check if the need exists before updating
                if ($need) {
                    if ($need['code'] == '32') {
                        $tempNeeds[$key]->temp_end_date = $tlcDate;
                    } else if ($need['code'] == '33') {
                        $tempNeeds[$key]->temp_end_date = $phrDate;
                    } else if ($need['code'] == '34') {
                        $tempNeeds[$key]->temp_end_date = $yahDate;
                    }

                    $update = Need::find($need['id']);
                    $update->temp_end_date = $need['temp_end_date'];
                    $update->save();
                }
            }



            // Set the redirect based on whether the registration has been removed or not
            if ( !is_null($removed_date) ) {
                $redirect = route('home');
            } else {
                $redirect = route('customers.show', ['customer' => $customer->id]);
            }

            // Response JSON
            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'redirect_url' => $redirect,
            ]);


/*                        return response()->json([
                            'success' => true,
                            'message' => 'successfully retrieved data',
                            'data' => [
                                //'sap_bp_ref' => $sap_bp_ref,
                                //'customer_ref' => $customer_ref,
                                //'source' => $source,
                                //'consent_date' => $consent_date,
                                //'removed_date' => $removed_date,
                                //'recipient_name' => $recipient_name,
                                'arrayData' => $arrayData,
                                //'needs' => $needs,
                                //'needCodes' => $needCodes,
                                //'currentNeeds' => $currentNeeds,
                                //'currentNeedCodes' => $currentNeedCodes,
                                //'addNeeds' => $addNeeds,
                                //'removeNeeds' => $removeNeeds,
                                //'services' => $services,
                                //'serviceCodes' => $serviceCodes,
                                //'currentServices' => $currentServices,
                                //'currentServiceCodes' => $currentServiceCodes,
                                //'addServices' => $addServices,
                                //'removeServices' => $removeServices,
                                //'commonServices' => $commonServices,
                                //'psr_id' => $psr_id,
                                //'customer' => $customer->id,
                                'attributeCodes' => $attributeCodes,
                                'currentAttributeCodes' => $currentAttributeCodes,
                                'addAttributes' => $addAttributes,
                                'removeAttributes' => $removeAttributes,
                            ],
                            'redirect_url' => route('customers.show', ['customer' => $customer->id])
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function search(Request $request)
    {

        try {

            // Store the search term in a variable and validate the entry
            $data = [ 'search' => $request['search']];

            $rules = [
                'search' => 'required|regex:/^[a-zA-Z0-9\'\-\s]+$/',
            ];

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                throw new \Exception('The field may only contain letters, numbers, single quotes, hyphens, and spaces.');
            }


            // Search query from request
            $query = $request->query('search');

            //dd($request->query('search'));

            // Check if there is a query and find the matching record
            if ($query) {
                $results = Customer::where('id', 'like', '%' . $query . '%')
                    ->orWhere('SAP_reference', 'like', '%' . $query . '%')
                    ->orWhere('primary_forename', 'like', '%' . $query . '%')
                    ->orWhere('primary_surname', 'like', '%' . $query . '%')
                    ->orWhere('secondary_forename', 'like', '%' . $query . '%')
                    ->orWhere('secondary_surname', 'like', '%' . $query . '%')
                    ->get();
            } else {
                $results = [];
            }

            // Store the request in the session and then clear it to reset the search box
            $request->session()->flash('searchQuery', $query);
            $request->session()->forget('searchQuery');

            //dd($results);

            // Return the view
            return view('customers.index', ['customers' => $results]);


        } catch (\Exception $e) {
            // Catch any exceptions and return an error response
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }


}
