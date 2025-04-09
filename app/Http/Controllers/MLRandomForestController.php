<?php

namespace App\Http\Controllers;

use App\Jobs\MLTrainRandomForestModelJob;
use Illuminate\Http\Request;
use App\Models\MLModelStorage;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Serializers\Native;
use Log;

class MLRandomForestController extends Controller
{
    public function trainModel()
    {

        // Dispatch the job to run in the background
        MLTrainRandomForestModelJob::dispatch();

        return response()->json([
            'message' => 'Model training started in the background.  You will be notified when it is complete.',
        ]);

    }

    public function predict(Request $request) {

        // Validate the inputs
        $request->validate([
            'house_number' => 'sometimes|string|nullable',
            'house_name' => 'sometimes|string|nullable',
            'street' => 'required|string',
            'town' => 'sometimes|string|nullable',
            'parish' => 'sometimes|string|nullable',
            'county' => 'sometimes|string|nullable',
            'postcode' => 'required|string|min:4|max:8',
        ]);

        // Get the latest trained model
        $modelRecord = MLModelStorage::latest()->first();
        if (!$modelRecord) {
            return response()->json(['message' => 'No trained model found.'], 400);
        }

        // Deserialize the model
        $serializer = new Native();
        $estimator = $serializer->deserialize($modelRecord->model_data);

        // Prepare the input data
        $inputSample = [
            $request->input('house_number') ?? '',
            $request->input('house_name') ?? '',
            $request->input('street'),
            $request->input('town') ?? '',
            $request->input('parish') ?? '',
            $request->input('county') ?? '',
            $request->input('postcode'),
        ];

        // Convert the input sample to an unlabeled dataset
        $dataset = new Unlabeled($inputSample);

        // Make prediction
        $prediction = $estimator->predict($dataset);

        return response()->json(['predicted_uprn' => $prediction[0]], 200);
    }
}
