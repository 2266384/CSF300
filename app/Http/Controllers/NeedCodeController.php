<?php

namespace App\Http\Controllers;

use App\Models\NeedCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NeedCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $needCodes = NeedCode::all();
        return view('needcodes.index', ['needcodes' => $needCodes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('needcodes.create');
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
                'code' => 'required|integer|unique:need_codes,code',   // Ensures the need code is unique
                'description' => 'required|string|max:255',
                //'active' => 'optional|boolean',
            ];

            $messages = [
                'code.required' => 'Need code is required',
                'code.integer' => 'Need code must be an integer',
                'code.unique' => 'The Need Code already exists',
                'description.required' => 'Need description is required'
            ];

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $needcode = new NeedCode();
            $needcode->code = $data['code'];
            $needcode->description = $data['description'];
            $needcode->active =$data['active'];
            $needcode->save();

            // Set the redirect based on whether the registration has been removed or not
            $redirect = route('needcodes.index');

            // Response JSON
            return response()->json([
                'success' => true,
                'message' => 'Need code created successfully',
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
    public function edit(NeedCode $needcode)
    {
        return view('needcodes.edit', compact('needcode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //dd($request->all());

        $request->validate([
            'code' => 'required|integer',
            'description' => 'required|string|max:255',
        ],
        [
            'code.required' => 'Need code is required',
            'code.integer' => 'Need code must be an integer',
            'description.required' => 'Need description is required'
        ]);

        $input = $request->all();

        $needcode = NeedCode::find($input['code']);

        //dd(is_null($input['active']));

        $update = $needcode;
        $update->description = $input['description'];
        $update->active = $input['active'];
        $update->save();

        return redirect()
            ->route('needcodes.index')
            ->with('success', 'Need updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
