<?php

namespace App\Services;

use App\Models\MLModelStorage;
use App\Models\Property;
use Rubix\ML\Classifiers\KDNeighbors;
use Rubix\ML\CrossValidation\Reports\MulticlassBreakdown;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Transformers\TextNormalizer;
use Rubix\ML\Transformers\TfIdfTransformer;
use Rubix\ML\Transformers\WordCountVectorizer;

class MLTrainKDNService
{

    protected $kdn;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        // Initialise the KDN
        $this->kdn = new KDNeighbors(5, false);
    }


    /**
     * Function to train and save the model as a KDNeighbour
     * Fails applying the vectorisation of the dataset silently and with no error at line 88!!!
     * @param string $input
     * @return mixed
     */
    public function trainAndSaveModel() {

        try {
            // Get all the properties
            $properties = Property::all();

            // Prepare the dataset for RubixML
            $trainingData = [];
            $labels = [];

            // Get all the full address strings and the matching ids
            foreach ($properties as $property) {
                $trainingData[] = implode(' ',
                    array_filter([
                        $property->house_number,
                        $property->house_name,
                        $property->street,
                        $property->town,
                        $property->parish,
                        $property->county,
                        $property->postcode,
                    ])
                );        // The address string to match
                $labels[] = $property->id;                       // The id of the property
            }


            // Remove commas from the address and the space in the postcode
            $cleanedTrainingData = array_map(function ($address) {
                return str_replace(",", "", preg_replace("/\s+(?=[^\s]+$)/", "", $address));
            }, $trainingData);

            // Create a labeled dataset
            $dataset = new Labeled($cleanedTrainingData, $labels);


            // Create a labeled dataset
            //$dataset = new Labeled($trainingData, $labels);

            // Converts text values to uppercase
            $transformer = new TextNormalizer(true);
            $dataset->apply($transformer);

            dump($dataset->samples()[0]);

            // Convert our text values to numerical vectors
            $vectorizer = new WordCountVectorizer(100000, 1, 0.5);
            $tfidf = new TfIdfTransformer();

            try {
                $vectorizer->fit($dataset);
                dump("Vocabulary Size:", count($vectorizer->vocabularies()[0]));
                $dataset->apply($vectorizer);
                dump($dataset->samples());  // Debug after vectorizing
            } catch (\Exception $e) {
                dump($e->getMessage());  // Show any error
            }

            dump('Finished Apply');

           // $vectorizer->fit($dataset);
            //$dataset->apply($vectorizer);   // Transforms data in place

            //dump($dataset->samples()[0]);

            $tfidf->fit($dataset);
            $dataset->apply($tfidf);        // Transforms in place

            // Train the KDN model
            $this->kdn = new KDNeighbors(5, false);
            $this->kdn->train($dataset);

            // Get the training metrics
            $predictions = $this->kdn->predict($dataset);
            $report = new MulticlassBreakdown();
            $results = $report->generate($predictions, $labels);

            //dd($results['overall']);

            // Get the individual metrics
            $overall = $results['overall'];
            $accuracy = $overall['accuracy'] ?? 0;
            $precision = $overall['precision'] ?? 0;
            $recall = $overall['recall'] ?? 0;
            $f1score = $overall['f1score'] ?? 0;

            // Serialize the trained model
            $serializedModel = serialize($this->kdn);

            // Save the Dataset and metrics in the database
            MLModelStorage::updateOrCreate(
                ['name' => 'address_match'],
                [
                    'name' => 'address_match',
                    'model_data' => $serializedModel,
                    'accuracy' => $accuracy,
                    'precision' => $precision,
                    'recall' => $recall,
                    'f1score' => $f1score
                ]
            );

            return [
                'success' => true,
                'message' => 'Model trained and stored successfully.'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

    }




    public function findClosestMatch(string $input) {

        // Retrieve the model from the database
        $mlModel = MLModelStorage::where('name',  'address_match')->first();

        if(!$mlModel) {
            // Train and save the model if it doesn't exist
            $this->trainAndSaveModel();
            $mlModel = MLModelStorage::where('name',  'address_match')->first();
        }

        // Second check for model in case the model still doesn't exist
        if (!$mlModel) {
            return null;
        }

        // Deserialize the stored model
        $this->kdn = unserialize($mlModel->model_data);

        $inputText = new Unlabeled([[$input]]);

        // Predict the nearest address
        $predictedLabel = $this->kdn->predict($inputText);

        // Get the ID of the closest match
        return Property::find($predictedLabel[0]);

    }
}
