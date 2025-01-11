<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Need;
use App\Models\Registration;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
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
            ];

            // Validation rules
            $rules = [
                'consent_date' => 'nullable|date|after_or_equal:today',
                'remove_dated' => 'nullable|date|after_or_equal:today',
                'recipient_name' => 'required|max:255',
                'psr_id' => 'required|numeric',
                'arrayData' => 'required|array|min:1',
            ];

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                throw new \Exception('Validation failed');
            }

            // Access regular data from FormData
            $customer_ref = $request->input('customer_ref');
            $consent_date = $request->input('consent_date');
            $removed_date = $request->input('removed_date');
            $recipient_name = $request->input('recipient_name');
            $psr_id = $request->input('psr_id');


            // Get the registration and customer associated with the PSR ID
            $registration = Registration::findorfail($psr_id);
            $customer = Customer::findorfail($customer_ref);


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

            $updateType = User::class;

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
                    $this->removeAttribute($currentAttribute);
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
                        $this->removeAttribute($removeAttribute);
                    }
                }

                if ( !blank($addAttributes) ) {
                    foreach ($addAttributes as $addAttribute) {

                        // Add ID and Update Type to array
                        $addAttribute['psr_id'] = $psr_id;
                        $addAttribute['updateType'] = $updateType;

                        $this->addAttribute($addAttribute);
                    }
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

/*
                        return response()->json([
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
                                'needs' => $needs,
                                //'needCodes' => $needCodes,
                                //'currentNeeds' => $currentNeeds,
                                'currentNeedCodes' => $currentNeedCodes,
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

    protected function addAttribute(Array $data) {

        if ( $data['type'] == 'need' ) {
/*
            // Check to see if we have a historic code for this and if not create a new one
            try {
                $id = Need::where('registration_id', $data['psr_id'])
                    ->where('code', $data['code'])
                    ->where('active', 0)
                    ->firstOrFail();

                $need = Need::find($id);
                    $need->update(['active' => 1]);
            }
            catch(ModelNotFoundException $e) {
*/
                Need::create([
                    'registration_id' => $data['psr_id'],
                    'code' => $data['code'],
                    'lastupdate_id' => 1,
                    'lastupdate_type' => $data['updateType'],
                ]);

//            }

        } else if ( $data['type'] == 'service' ) {

            // Check to see if we have a historic code for this and if not create a new one
/*            try {
                $id = Service::where('registration_id', $data['psr_id'])
                    ->where('code', $data['code'])
                    ->where('active', 0)
                    ->firstOrFail();

                Service::find($id)->update(['active' => 1]);
            }
            catch(ModelNotFoundException $e) {
*/
                Service::create([
                    'registration_id' => $data['psr_id'],
                    'code' => $data['code'],
                    'lastupdate_id' => 1,
                    'lastupdate_type' => $data['updateType'],
                ]);

//            }
        }
    }

    protected function removeAttribute(Array $data) {

        if ( $data['type'] == 'need' ) {

            Need::findorfail($data['id'])
                ->update(['active' => 0]);

        } else if ( $data['type'] == 'service' ) {

            Service::findorfail($data['id'])
                ->update(['active' => 0]);

        }

    }

}
