<?php

namespace App\Http\Controllers;

use App\Models\ServiceCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $servicecode = ServiceCode::all();
        return view('servicecodes.index', ['servicecodes' => $servicecode]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('servicecodes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {

            $data = [
                'code' => $request->input('code'),
                'description' => $request->input('description'),
                'active' => $request->input('active'),
            ];

            $rules = [
                'code' => 'required|string|unique:need_codes,code',   // Ensures the need code is unique
                'description' => 'required|string|max:255',
                //'active' => 'optional|boolean',
            ];

            $messages = [
                'code.required' => 'Service code is required',
                'code.string' => 'Service code must be a string',
                'code.unique' => 'The Service Code already exists',
                'description.required' => 'Service description is required'
            ];

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $servicecode = new ServiceCode();
            $servicecode->code = $data['code'];
            $servicecode->description = $data['description'];
            $servicecode->active =$data['active'];
            $servicecode->save();

            // Set the redirect based on whether the registration has been removed or not
            $redirect = route('servicecodes.index');

            // Response JSON
            return response()->json([
                'success' => true,
                'message' => 'Service code created successfully',
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceCode $servicecode)
    {
        return view('servicecodes.edit', compact('servicecode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //dd($request->all());

        $request->validate([
            'code' => 'required|string',
            'description' => 'required|string|max:255',
        ],
        [
            'code.required' => 'Service code is required',
            'code.string' => 'Service code must be a string',
            'description.required' => 'Service description is required'
        ]);

        $input = $request->all();

        $servicecode = ServiceCode::find($input['code']);

        //dd($servicecode);

        $update = $servicecode;
        $update->description = $input['description'];
        $update->active = $input['active'];
        $update->save();

        return redirect()
            ->route('servicecodes.index')
            ->with('success', 'Service updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
