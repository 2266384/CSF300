<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\Responsibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organisations = Organisation::all();
        return view('organisations.index', ['organisations' => $organisations]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('organisations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $data = [
                'name' => $request->input('name'),
                'active' => $request->input('active'),
            ];

            $rules = [
                'name' => 'required|string|min:5|max:255',   // Ensures the need code is unique
                'active' => 'required|boolean',
            ];

            $messages = [
                'name.required' => 'Organisation name is required',
                'name.min' => 'Organisation name must be at least 5 characters',
                'name.max' => 'Organisation name cannot be longer than 255 characters',
                'active.required' => 'Active status is required',
            ];

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $organisation = new Organisation();
            $organisation->name = $data['name'];
            $organisation->active =$data['active'];
            $organisation->save();

            // Set the redirect based on whether the registration has been removed or not
            $redirect = route('organisations.index');

            // Response JSON
            return response()->json([
                'success' => true,
                'message' => 'Organisation created successfully',
                'redirect_url' => $redirect,
            ]);

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
    public function show(Organisation $organisation)
    {
        return view('organisations.show', ['organisation' => $organisation]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation)
    {
        return view('organisations.edit', ['organisation' => $organisation]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        try {

            // Decode the array data (sent as JSON string)
            $propertyData = json_decode($request->input('propertyData'), true);

            // Check the decode was successful
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON');
            }

            $data = [
                'id' => $request->input('id'),
                'name' => $request->input('name'),
                'active' => $request->input('active'),
                'propertyData' => $propertyData,
            ];

            // Validation rules
            $rules = [
                'id' => 'required|numeric|exists:organisations,id',
                'name' => 'required|string|min:5|max:255',
                'active' => 'required|boolean',
                'propertyData' => 'sometimes|array',
            ];

            $messages = [
                'id.required' => 'Organisation id is required',
                'id.numeric' => 'Organisation id must be numeric',
                'id.exists' => 'Organisation id does not exist',
                'name.required' => 'Organisation name is required',
                'name.min' => 'Organisation name must be at least 5 characters',
                'name.max' => 'Organisation name cannot be longer than 255 characters',
                'active.required' => 'Active status is required',
                'propertyData.required' => 'Property data is required',
                'propertyData.array' => 'Property data must be an array',
            ];

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get the current postcodes the organisation is responsible for
            $currentPostcodes = Responsibility::where('organisation', $data['id'])->get()->toArray();
            $pc = array_column($currentPostcodes, 'postcode');


            // Filter postcodes that do not exist in the existing postcodes
            $addProperties = array_filter($propertyData, function ($postcode) use ($pc) {
                return !in_array($postcode, $pc);
            });

            // Get all the postcodes that have been added to the list
            //$addProperties = array_diff($propertyData, $currentPostcodes);
            foreach ($addProperties as $addProperty) {
                $responsibility = new Responsibility();
                $responsibility->organisation = $data['id'];
                $responsibility->postcode = $addProperty;
                $responsibility->save();
            }


            // Get all the postcodes that have been removed from the list
            $removeProperties = array_filter($currentPostcodes, function ($item) use ($propertyData) {
                return !in_array($item['postcode'], $propertyData);
            });

            foreach ($removeProperties as $removeProperty) {

                $property = Responsibility::find($removeProperty['id']);
                $property->delete();
            }

            $organisation = Organisation::find($data['id']);

            //dd(is_null($input['active']));

            $update = $organisation;
            $update->name = $data['name'];
            $update->active = $data['active'];
            $update->save();

            // Set the redirect based on whether the registration has been removed or not
            $redirect = route('organisations.index');

            // Response JSON
            return response()->json([
                'success' => true,
                'message' => 'Organisation updated successfully',
                'redirect_url' => $redirect,
            ]);

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
}
