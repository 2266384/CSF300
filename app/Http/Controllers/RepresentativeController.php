<?php

namespace App\Http\Controllers;

use App\Models\Representative;
use App\Services\TokenService;
use Database\Seeders\RepresentativesTableSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RepresentativeController extends Controller
{

    protected $tokenService;

    /**
     * Inject the TokenService to the Representative Object
     */
    public function __construct(TokenService $tokenService) {
        $this->tokenService = $tokenService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $representatives = Representative::all();
        return view('representatives.index', ['representatives' => $representatives]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('representatives.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $data = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'organisation' => $request->input('organisation'),
                'token' => $request->input('token'),
                'active' => $request->input('active'),
            ];

            $rules = [
                'name' => 'required|string|min:5|max:255',
                'email' => 'required|email|unique:representatives,email',
                'password' => ['required', Password::defaults()],
                'organisation' => 'required|integer|exists:organisations,id',
                'token' => 'required|boolean',
                'active' => 'required|boolean',
            ];

            $messages = [
                'name.required' => 'The name field is required.',
                'name.string' => 'The name must be a string.',
                'name.min' => 'The name must be at least 5 characters.',
                'name.max' => 'The name may not be greater than 255 characters.',
                'email.required' => 'The email field is required.',
                'email.email' => 'The email must be a valid email address.',
                'password.required' => 'The password field is required.',
                'organisation.required' => 'The organisation field is required.',
                'token.required' => 'The api-token field is required.',
                'token.boolean' => 'The api-token must be a valid boolean.',
                'active.required' => 'The active field is required.',
                'active.boolean' => 'The active must be a boolean.',
            ];

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create the new Representative model
            $representative = new Representative();
            $representative->name = $data['name'];
            $representative->email = $data['email'];
            $representative->password = Hash::make($data['password']);
            $representative->organisation_id = $data['organisation'];
            $representative->active = $data['active'];
            $representative->save();

            $newRepresentative = Representative::where('email', $data['email'])->first();


            // Check if we want to create an API token for the Representative
            if($data['token'] == 1) {
                $this->tokenService->generateToken($newRepresentative, 'RepresentativeToken');
            };

            $redirect = route('representatives.index');

            // Response JSON
            return response()->json([
                'success' => true,
                'message' => 'Representative created successfully',
                'token' => $newRepresentative->api_token,
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
    public function show(Representative $representative)
    {
        return view('representatives.show', ['representative' => $representative]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Representative $representative)
    {
        return view('representatives.edit', ['representative' => $representative]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //dd($request->all());

        $request->validate([
            'id' => 'required|integer|exists:representatives,id',
            'name' => 'required|string|min:5|max:255',
            'email' => ['required', 'email',
                Rule::unique('representatives', 'email')->ignore($request['id'])
                ],  // Check for uniqueness except current rep
            'password' => ['sometimes', Password::defaults(), 'nullable'],
            'organisation' => 'required|integer|exists:organisations,id',
            'api-token' => 'sometimes|string|size:40|nullable',
            'active' => 'required|boolean',
            'api-read' => 'required|boolean',
            'api-write' => 'required|boolean',
        ],
        [
            'id.required' => 'The id field is required.',
            'id.integer' => 'The id must be an integer.',
            'id.exists' => 'The id does not exist.',
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.min' => 'The name must be at least 5 characters.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'organisation.required' => 'The organisation field is required.',
            'api-token.size' => 'The api-token must be exactly 40 characters.',
            'active.required' => 'The active field is required.',
            'active.boolean' => 'The active must be a boolean.',
            'api-read.required' => 'The api-token field is required.',
            'api-read.boolean' => 'The api-token must be a valid boolean.',
            'api-write.required' => 'The api-token field is required.',
            'api-write.boolean' => 'The api-token must be a valid boolean.',
        ]);

        $input = $request->all();

        $representative = Representative::find($input['id']);

        $update = $representative;
        $update->name = $input['name'];
        $update->email = $input['email'];
        // If we have a new password hash it and update it
        if(isset($input['password'])) {
            $update->password = Hash::make($input['password']);
        }
        $update->organisation_id = $input['organisation'];
        $update->active = $input['active'];
        $update->save();


        //Compile the abilities from the request into one array
        $abilities = [];

        if ( $request->input('api-read') == 1) {
            $abilities[] = 'read';
        };

        if ( $request->input('api-write') == 1) {
            $abilities[] = 'write';
        };

        //dd($representative, !empty($input['api-token']));

        // Check if we've made the representative inactive and remove their tokens
        if (!$input['active']) {

            $representative->tokens()->delete();

        } else {

            // If we've created a new token then delete any existing tokens and create a new one
            if (!empty($input['api-token'])) {

                //dd($input['api-token'], $abilities);

                // Delete any existing tokens - there should only be one token per representative
                $representative->tokens()->where('name', 'RepresentativeToken')->delete();

                // Create a new token
                $representative->tokens()->create([
                    'name' => 'RepresentativeToken',
                    'token' => hash('sha256', $input['api-token']),
                    'abilities' => $abilities,
                ]);

            } else {

                // Get the current token for the representative
                $token = $representative->tokens()->where('tokenable_id', $representative->id)
                    ->where('name', 'RepresentativeToken')->first();

                // Check if the token abilities is currently null or not
                $tokenAbilities = isset($token->abilities) ? $token->abilities : null;

                if (!is_null($token)) {

                    if (is_null($tokenAbilities) || $tokenAbilities != $abilities) {

                        // Get the existing hashed token so we can create a new record with the same value
                        $hashedToken = $token->token;

                        // Remove the existing token
                        $token->delete();

                        $representative->tokens()->create([
                            'name' => 'RepresentativeToken',
                            'token' => $hashedToken,
                            'abilities' => $abilities,
                        ]);
                    };
                }

            }
        }

        return redirect()
            ->route('representatives.index')
            ->with('success', 'Representative updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
