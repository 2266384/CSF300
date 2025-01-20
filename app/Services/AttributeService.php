<?php

namespace App\Services;

use App\Models\Action;
use App\Models\NeedCode;
use App\Models\ServiceCode;
use Illuminate\Support\Facades\DB;

class AttributeService {



    /**
     * Function to compile a list of Need and Service codes and create
     * a unique index value
     * @return array allcodesWithIndex
     */
    public function indexedAttributes() {

        // Get the Needs and Services and merge them into an array
        $needs = Collect(NeedCode::all()
            ->where('active', 1)
            ->map(function ($need) {
            return [
                'code' => $need->code,
                'description' => $need->description,
                'type' => 'need',
                'state' => 'enabled'
            ];
        })->toArray());

        $services = Collect(ServiceCode::all()
            ->where('active', 1)
            ->map(function ($service) {
            return [
                'code' => $service->code,
                'description' => $service->description,
                'type' => 'service',
                'state' => 'disabled'
            ];
        })->toArray());

        // Put all the needs and services into an array of arrays
        $allcodes = $needs->merge($services)->toArray();


        $allcodesWithIndex = collect(array_map(function ($item, $index) {
            $item['index'] = $index + 1;
            return $item;
        }, $allcodes, array_keys($allcodes)));

        return $allcodesWithIndex;
    }

    /**
     * Retrieves all the CURRENT attributes for the Customer Reference provided
     * @param $customer
     * @return mixed
     */
    function currentAttributes($customer): mixed
    {

        $allcodesWithIndex = $this->indexedAttributes();

        //dd($customer->needs->where('active', '=', 1));

        $needs = $customer->needs
            ->where('active', '=', 1)
            ->map(function ($need) use ($allcodesWithIndex) {
            // Find matching item from $allcodesWithIndex based on code
            $indexValue = Collect($allcodesWithIndex)->firstWhere('code', $need['code']);


            return [
                'index' => $indexValue['index'],
                'code' => $need->code,
                'description' => $need->description->description,
                'temp_end_date' => $need->temp_end_date,
                'valid_from' => $need->valid_from,
                'valid_to' => $need->valid_to,
                'type' => 'need',
                //'state' => $need->state,
            ];
        })->toArray();

        //dd($needs);

        $services = $customer->services
            ->where('active', '=', 1)
            ->map(function ($service) use ($allcodesWithIndex) {
            // Find matching item from $allcodesWithIndex based on code
            $indexValue = Collect($allcodesWithIndex)->firstWhere('code', $service['code']);


            return [
                'index' => $indexValue['index'],
                'code' => $service->code,
                'description' => $service->description->description,
                'temp_end_date' => $service->temp_end_date,
                'valid_from' => $service->valid_from,
                'valid_to' => $service->valid_to,
                'type' => 'service',
                //'state' => $service->state,
            ];
        })->toArray();

        $sorted = collect(array_merge($needs, $services))->sortBy('valid_from')->sortBy('valid_to');

        return $sorted->values()->all();
    }

    /**
     * Return all the HISTORIC attributes for the Customer Reference provided
     * @param $customer
     * @return mixed
     */
    function previousAttributes($customer): mixed
    {

        //dd($customer->needs->where('active', '=', 1));
        $registration = $customer->registrations->where('active', '=', 1)->first()->id;

        $needs = (DB::table('needs_history')
            ->join('need_codes', 'need_codes.code', '=', 'needs_history.code')
            ->where('needs_history.registration_id', $registration)
            ->where('needs_history.active', 1)
            ->select('need_codes.code', 'need_codes.description', 'needs_history.temp_end_date', 'needs_history.valid_from', 'needs_history.valid_to')
            ->get()
        )->map(function ($need) {
            return [
                'code' => $need->code,
                'description' => $need->description,
                'temp_end_date' => $need->temp_end_date,
                'valid_from' => $need->valid_from,
                'valid_to' => $need->valid_to,
                'type' => 'need',
                //'state' => $need->state,
            ];
        })->toArray();

        //dd($needs);

        $services = (DB::table('services_history')
            ->join('service_codes', 'service_codes.code', '=', 'services_history.code')
            ->where('services_history.registration_id', $registration)
            ->where('services_history.active', 1)
            ->select('service_codes.code', 'service_codes.description', 'services_history.temp_end_date', 'services_history.valid_from', 'services_history.valid_to')
            ->get()
        )->map(function ($service) {
            return [
                'code' => $service->code,
                'description' => $service->description,
                'temp_end_date' => $service->temp_end_date,
                'valid_from' => $service->valid_from,
                'valid_to' => $service->valid_to,
                'type' => 'service',
                //'state' => $service->state,
            ];
        })->toArray();

        //dd($needs);

        $sorted = collect(array_merge($needs, $services))->sortBy('valid_from')->sortBy('valid_to');

        return $sorted->values()->all();
    }

    function getAvailableAttributes($customer) {

        $allcodesWithIndex = $this->indexedAttributes()->toArray();

        // Get the existing needs and services for the customer
        $existing_needs = $this->currentAttributes($customer);

        // Compare the arrays of arrays and remove the duplicates to create our selectable attributes
        $arrdiff = array_udiff($allcodesWithIndex, $existing_needs, function ($a, $b) {
            return $a['code'] <=> $b['code'];
        });

        //dd(collect($arrdiff));

        $array = collect($arrdiff)->map(function ($item) {
            return [
                'index' => $item['index'],
                'code' => $item['code'],
                'description' => $item['description'],
                'type' => $item['type'],
                'state' => $item['state'],
            ];
        })->toArray();

        // Put the array into a collection and remove duplicate codes
        $response = collect($array)->unique('code');

        //dd($allcodesWithIndex);
        //dd($response);
        //dd($existing_needs);

        return $response;
    }


    function attributesWithActions() {

        // Array of Actions
        $actions = Action::all()
            ->where('active', 1)
            ->map(function ($action) {
                return [
                    'sourcecode' => $action->sourcecode,
                    'targetcode' => $action->targetcode,
                    'action' => $action->action,
                ];
            });

        $joined = $this->indexedAttributes()->map(function ($item) use ($actions) {

            // Find matching items based on sourcecode
            $matchingItems = $actions->filter(function ($secondItem) use ($item) {
                return $secondItem['sourcecode'] == $item['code'];
            });

            // Fill in missing items with blank values
            if($matchingItems->isEmpty()) {
                return array_merge($item, [
                    'sourcecode' => null,
                    'targetcode' => null,
                    'action' => null,
                ]);
            }

            // Merge found matches otherwise return the original item
            return $matchingItems->map(function ($match) use ($item) {
                return array_merge($item, $match);
            });
        });

        $flattened = $joined->reduce(function ($carry, $item) {
            if($item instanceof \Illuminate\Support\Collection) {
                return $carry->merge($item);
            }

            return $carry->push($item);
        }, collect());

    return $flattened;

    }


}
