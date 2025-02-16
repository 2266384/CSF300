<?php

use App\Models\Customer;
use App\Models\Need;
use App\Models\Property;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function Pest\Laravel\json;

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

    function addAttribute(Array $data)
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

if (! function_exists('latestActivity')) {
    function latestActivity($user) {

        //dd($user->updatedNeeds);

        // Get the needs and services from the last 90 days
        $needUpdates = $user->updatedNeeds()
            ->where('valid_from', '>=', [Carbon::now()->subDays(90)->toDateString()])
            ->get();

        $serviceUpdates = $user->updatedservices()
            ->where('valid_from', '>=', [Carbon::now()->subDays(90)->toDateString()])
            ->get();

        // Merge the collections together and sort them
        $allUpdates = $needUpdates->merge($serviceUpdates)->sortByDesc('valid_from');

        return $allUpdates;

    }

}

if (! function_exists('isActiveRoute')) {
    function isActiveRoute($pattern, $output = 'active') {
        return request()->is($pattern) ? $output : '';
    }
}

/**
 * Returns the address of the provided Property as a comma
 * separated string without the postcode
 */
if (! function_exists('propertyAddress')) {
    function propertyAddress(Property $property) {

        $address = [
            'House_Number' => $property->house_number,
            'House_Name' => $property->house_name,
            'Street' => $property->street,
            'Town' => $property->town,
            'Parish' => $property->parish,
            'County' => $property->county,
        ];

        return implode(', ', $address);

    }
}

/**
 * Removed non-alphanumeric characters from the provided string and
 * returns a 'clean' copy e.g '123-45 abc#' returns '12345abc'
 */
if (! function_exists('cleanString')) {
    function cleanString($string) {

        // Replace all characters in the string that aren't alphanumeric
        $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', $string);

        return $cleaned;
    }
}

/**
 * Function to return a collection of Property objects matched
 * using a defined set of logical steps
 *
 *      IF 'ID' is provided use this,
 *      ELSE IF 'UPRN' is provided use this,
 *      ELSE search using a combination of provided address fields
 */

if (! function_exists('matchProperties')) {
    function matchProperties(Request $request) {

        // Get the query parameters
        $id = $request->query('id');
        $uprn = $request->query('uprn');
        // Remove all characters except alphanumerics from the address strings
        $houseno = cleanString($request->query('houseno'));
        $housename = cleanString($request->query('housename'));
        $street = cleanString($request->query('street'));
        $town = cleanString($request->query('town'));
        $parish = cleanString($request->query('parish'));
        $county = cleanString($request->query('county'));
        $postcode = cleanString($request->query('postcode'));

        // Set our properties variable to an empty collection
        $properties = collect();

        // Search by ID if provided
        if($id) {
            // Use get() for the search so that the return is a COLLECTION
            $properties = Property::where('id',$id)->get();
        }
        // Search by UPRN if we have it
        else if($uprn) {
            $properties = Property::where('uprn', $uprn)->get();
        }
        // If we haven't found the property using the UPRN check for the address
        else {
            // Find any properties that contain the matching address strings
            $properties = DB::table('properties')
                ->when($houseno, fn($query) => $query->whereRaw("isnull(dbo.RemoveNonAlphaNumeric(house_number),'') like ?", [$houseno])
                )
                ->when($housename, fn($query) => $query->whereRaw("isnull(dbo.RemoveNonAlphaNumeric(house_name),'') like ?", [$housename])
                )
                ->when($street, fn($query) => $query->whereRaw("isnull(dbo.RemoveNonAlphaNumeric(street),'') like ?", [$street])
                )
                ->when($town, fn($query) => $query->whereRaw("isnull(dbo.RemoveNonAlphaNumeric(town),'') like ?", [$town])
                )
                ->when($parish, fn($query) => $query->whereRaw("isnull(dbo.RemoveNonAlphaNumeric(parish),'') like ?", [$parish])
                )
                ->when($county, fn($query) => $query->whereRaw("isnull(dbo.RemoveNonAlphaNumeric(county),'') like ?", [$county])
                )
                ->when($postcode, fn($query) => $query->whereRaw("isnull(dbo.RemoveNonAlphaNumeric(postcode),'') like ?", [$postcode])
                )
                ->get();
        }

        // If we've found some properties return them
        if($properties->isNotEmpty()) {
            // Get the return columns
            $properties = $properties->map(function ($property) {
                return[
                    'ID' => $property->id,
                    'UPRN' => $property->uprn,
                    'House No' => $property->house_number,
                    'House Name' => $property->house_name,
                    'Street' => $property->street,
                    'Town' => $property->town,
                    'Parish' => $property->parish,
                    'County' => $property->county,
                    'Postcode' => $property->postcode,
                ];
            });

        }

        return $properties;

    }
}

if (! function_exists('matchCustomerNames')) {
    function matchCustomerNames(Request $request)
    {

        //Get the data from the request and remove any non alphanumeric characters
        $data = [
            'primary_title' => cleanString($request['primary_title']),
            'primary_forename' => cleanString($request['primary_forename']),
            'primary_surname' => cleanString($request['primary_surname']),
            'secondary_title' => cleanString($request['secondary_title']),
            'secondary_forename' => cleanString($request['secondary_forename']),
            'secondary_surname' => cleanString($request['secondary_surname']),
            'request_primary_title' => cleanString($request['request_primary_title']),
            'request_primary_forename' => cleanString($request['request_primary_forename']),
            'request_primary_surname' => cleanString($request['request_primary_surname']),
            'request_secondary_title' => cleanString($request['request_secondary_title']),
            'request_secondary_forename' => cleanString($request['request_secondary_forename']),
            'request_secondary_surname' => cleanString($request['request_secondary_surname']),
        ];

        // Check if we have a match to either the Primary or Secondary Names
        // Exclude title, include first 3 characters for forename and full surname field
        $primaryMatches =
            //in_array($data['request_primary_title'], [$data['primary_title'], $data['secondary_title']]) &&
            in_array(
                Str::substr($data['request_primary_forename'], 0, 3),
                [
                    Str::substr($data['primary_forename'], 0, 3),
                    Str::substr($data['secondary_forename'], 0, 3)
                ]) &&
            in_array($data['request_primary_surname'], [$data['primary_surname'], $data['secondary_surname']]);

        $secondaryMatches =
            //in_array($data['request_secondary_title'], [$data['primary_title'], $data['secondary_title']]) &&
            in_array(
                Str::substr($data['request_secondary_forename'], 0, 3),
                [
                    Str::substr($data['primary_forename'], 0, 3),
                    Str::substr($data['secondary_forename'], 0, 3)
                ]) &&
            in_array($data['request_secondary_surname'], [$data['primary_surname'], $data['secondary_surname']]);

        // Default the match results to 'No Match'
        $matchResult = 'No Match';

        // Check if we have a match and update the result
        if ($primaryMatches && $secondaryMatches) {
            $matchResult = 'Full Match';
        } else if ($primaryMatches && !$secondaryMatches) {
            $matchResult = 'Primary Match';
        } else if (!$primaryMatches && $secondaryMatches) {
            $matchResult = 'Secondary Match';
        }

        return $matchResult;

    }
}

