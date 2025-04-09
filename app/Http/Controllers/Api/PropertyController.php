<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isEmpty;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Authenticate user
        $user = $request->user();

        // Get the properties the organisation is responsible for
        $properties = $user->represents->responsible_for;

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

        return response()->json($properties);
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
    public function show(Request $request, $id)
    {

        $data = [
            'id' => $id,
        ];

        // Validation rules
        $rules = [
            'id' => 'required|integer|exists:properties,id',
        ];

        $messages = [
            'id.required' => 'ID is required',
            'id.integer' => 'ID must be integer',
            'id.exists' => 'ID does not exist',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ],422);
        }

        // Get the user
        $user = $request->user();


        // Get the customers in occupancy in the properties
        $properties = $user->represents->responsible_for;

        $property = $properties->firstWhere('id', $id);

        // Get the return columns
        if ($property) {
            $singleProperty = [
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
        } else {
            $singleProperty = null;
        };

        return response()->json($singleProperty);

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
     * Gets the property based on a query parameter
     */
    public function getPropertyByQuery(Request $request) {

        $data = [
            'uprn' => $request->query('uprn'),
            'houseno' => $request->query('houseno'),
            'housename' => $request->query('housename'),
            'street' => $request->query('street'),
            'town' => $request->query('town'),
            'parish' => $request->query('parish'),
            'county' => $request->query('county'),
            'postcode' => $request->query('postcode'),
        ];

        // Validation rules
        $rules = [
            'uprn' => 'sometimes|integer|exists:properties,uprn|nullable',
            'houseno' => 'sometimes|string|nullable',
            'housename' => 'sometimes|string|nullable',
            'street' => 'sometimes|string|nullable',
            'town' => 'sometimes|string|nullable',
            'parish' => 'sometimes|string|nullable',
            'county' => 'sometimes|string|nullable',
            'postcode' => 'sometimes|string|nullable',
        ];

        $messages = [
            'uprn.integer' => 'UPRN must be integer',
            'uprn.exists' => 'UPRN does not exist',
            'housename.string' => 'Housename must be string',
            'street.string' => 'Street must be string',
            'town.string' => 'Town must be string',
            'parish.string' => 'Parish must be string',
            'county.string' => 'County must be string',
            'postcode.string' => 'Postcode must be string',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ],422);
        }

        // Return an error if the request doesn't have any parameters
        if(empty($request->query())) {
            return response()->json(['message' => 'No parameters provided'], 404);
        }

        // Send the request to the matchProperties helper function
        $properties = matchProperties($request);

        if($properties->isEmpty()) {
            return response()->json(['message' => 'No properties found'], 404);
        } else {
            return response()->json($properties);
        }

    }

}
