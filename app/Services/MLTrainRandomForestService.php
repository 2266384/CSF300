<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\MLModelStorage;
use Illuminate\Support\Facades\Log;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Classifiers\RandomForest;
use Rubix\ML\Classifiers\ClassificationTree;
use Rubix\ML\CrossValidation\Reports\MulticlassBreakdown;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Serializers\Native;

class MLTrainRandomForestService {

    public function trainAndStoreModel()
    {

        // Get all the properties
        //$properties = Property::all();
        $properties = Property::inRandomOrder()->limit(10000)->get();

        // Throw an error if there aren't any properties to train data with
        if ($properties->isEmpty()) {
            //return response()->json(['message' => 'No properties found.'], 400);
            Log::warning("No properties found");
            return;
        }

        // Prepare the data
        $samples = [];
        $labels = [];
        // Make sure the elements that aren't required are default to empty strings
        foreach ($properties as $property) {
            $samples[] = [
                //(string) ($property->house_number ?? ''),
                //(string) ($property->house_name ?? ''),
                (string) ($property->street),
                (string) ($property->town ?? ''),
                //(string) ($property->parish ?? ''),
                //(string) ($property->county ?? ''),
                //(string) ($property->postcode)
            ];
            //$labels[] = (string) ($property->uprn);
            $labels[] = (string) ($property->postcode);
        }

        // Create dataset
        $dataset = new Labeled($samples, $labels);
        // Split the data so we use 20% for training our model so we can calculate the metrics
        [$training, $testing] = $dataset->randomize()->split(0.8);

        // Initialise and train the model
        $estimator = new RandomForest(new ClassificationTree(10), 200, 0.1, true);
        $estimator->train($training);

        // Test model performance
        $predictions = $estimator->predict(new Unlabeled($testing->samples()));
        $report = new MulticlassBreakdown();
        // Get the overall results and put them into a variable
        $results = $report->generate($predictions, $testing->labels())->toArray()['overall'];
        $accuracy = $results['accuracy'];

        //dd($results);

        // Compare with the last model (if it exists)
        $lastMLModel = MLModelStorage::latest()->first();
        if ($lastMLModel && $accuracy < $lastMLModel->accuracy) {
            //return response()->json(['message' => 'New model performed worse, keeping the existing model.']);
            Log::info('New model performs worse, keeping the existing model.');
            return;
        }


        // Save the model
        $serializer = new Native();
        $serializedModel = $serializer->serialize($estimator);

        if (!$serializedModel || empty($serializedModel)) {
            Log::error("Model serialization failed. Skipping database insert.");
            return;
        }

        // Store the model in the database
        MLModelStorage::create([
            'name' => 'properties_random_forest',
            'model_data' => $serializedModel,
            'accuracy' => $accuracy,
            'precision' => $results['precision'],
            'recall' => $results['recall'],
            'f1_score' => $results['f1 score'],
        ]);

        $count = MLModelStorage::count();

        // Only keep the last 3 models
        /*
                $count = MLModelStorage::count();
                if ($count > 3) {
                    MLModelStorage::orderBy('id', 'desc')->skip(3)->take(PHP_INT_MAX)->delete();
                }
        */

        //return response()->json(['message' => "Model trained and stored successfully with accuracy: $accuracy."]);
        Log::info('New model trained and stored successfully with accuracy: ' . $accuracy);

    }

}
