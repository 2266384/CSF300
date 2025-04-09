<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Responsibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ResponsibilityController extends Controller
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

        $data = [
            'postcode' => $request['postcode'],
        ];

        // Validation rules
        $rules = [
            'postcode' => 'required|string|exists:properties,postcode',
        ];

        $messages = [
            'postcode.required' => 'Postcode is required.',
            'postcode.string' => 'Postcode must be a string.',
            'postcode.exists' => 'Postcode does not exist.',
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
        //$user = $request->user();
        $user = Auth::user();

        $organisation = $user->represents;

        $postcode = $data['postcode'];

        // Check if we're currently responsible for the postcode and, if not, add it in
        $postcodes = $organisation->responsible_for->pluck('postcode')->toArray();

        if(!in_array($postcode, $postcodes)) {
            $responsibility = new Responsibility();
            $responsibility->organisation = $organisation->id;
            $responsibility->postcode = $postcode;
            $responsibility->save();
        } else {
            return response()->json([
                'status' => 422,
                'message' => "Responsibility already exists"]);
        }

        // Response JSON
        return response()->json([
            'status' => 200,
            'message' => 'Responsibility created successfully',
        ],200);

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
    public function destroy(Request $request)
    {

        $user = Auth::user();
        $organisation = $user->represents;

        $responsibility = Responsibility::where('postcode', $request['postcode'])
            ->where('organisation', $organisation->id)
            ->first();

        if($responsibility) {
            $responsibility->delete();
        } else {
            return response()->json([
                'status' => 422,
                'message' => "Responsibility does not exist"]);
        }

        // Response JSON
        return response()->json([
            'status' => 200,
            'message' => 'Responsibility removed successfully',
        ],200);
    }

    /**
     * Create responsibilities for multiple postcodes
     */
    public function storeAll(Request $request)
    {

        // Get the request data
        $requests = $request->all();
        $responsibilityController = app(ResponsibilityController::class);


        // Iterate the requests and call the controller directly for each
        $responses = collect($requests)->map(function ($item) use ($responsibilityController) {

            // Turn the postcodes into individual array items
            $pc = ['postcode' => $item];

            $internalRequest = new Request($pc);

            $response = $responsibilityController->store($internalRequest);

            if ($response instanceof JsonResponse) {
                $status = $response->getData(true)['status'];
                $message = $response->getData(true)['message'] ?? 'Unknown error';
                $error = $response->getData(true)['error'] ?? '';

                return [
                    'status' => $status,
                    'message' => in_array($status, [200, 201, 204]) ? 'Responsibility created successfully' : $message,
                    'error' => $error,
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

    /**
     * Remove multiple property responsibilities
     */
    public function destroyAll(Request $request)
    {


        // Get the request data
        $requests = $request->all();
        $responsibilityController = app(ResponsibilityController::class);


        // Iterate the requests and call the controller directly for each
        $responses = collect($requests)->map(function ($item) use ($responsibilityController) {

            // Turn the postcodes into individual array items
            $pc = ['postcode' => $item];

            $internalRequest = new Request($pc);

            $response = $responsibilityController->destroy($internalRequest);

            if ($response instanceof JsonResponse) {
                $status = $response->getData(true)['status'];
                $message = $response->getData(true)['message'] ?? 'Unknown error';
                $error = $response->getData(true)['error'] ?? '';

                return [
                    'status' => $status,
                    'message' => in_array($status, [200, 201, 204]) ? 'Responsibility removed successfully' : $message,
                    'error' => $error,
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
