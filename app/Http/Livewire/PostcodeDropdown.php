<?php

namespace App\Http\Livewire;

use App\Models\Organisation;
use App\Models\Property;
use App\Models\Responsibility;
use Livewire\Component;

class PostcodeDropdown extends Component
{

    public $organisation;           // The Organisation
    public $properties = [];        // The postcodes selected for the Organisation

    /* Selection lists */
    public $areas;
    public $outcodes;
    public $incodes;

    /* Selected items */
    public $selectedArea = null;
    public $selectedOutcode = null;
    public $selectedIncode = null;


    public function mount(Organisation $organisation = null) {

        // Get the unique postcode area list
        $this->areas = Property::all()->pluck('PostcodeArea')->unique();

        // Set the Organisation
        $this->organisation = $organisation ?? 'default';


        // If the organisation is not set to default then load the current attributes
        if($this->organisation !== 'default') {

            $sortedProperties = $this->organisation->responsible_for->pluck('postcode')->unique()->toArray();
            sort($sortedProperties);

            $this->properties = $sortedProperties;

        }

    }

    /* Updates the dependent droopdown when selecting an Area */
    public function updatedSelectedArea($area) {

        $this->outcodes = Property::where('PostcodeArea', $area)->get()->pluck('Outcode')->unique();
        $this->selectedOutcode = null;
        $this->selectedIncode = null;
    }

    public function updatedSelectedOutcode($outcode) {

        $this->incodes = Property::where('Outcode', $outcode)->get()->pluck('Incode')->unique();
        $this->selectedIncode = null;
    }


    /**
     * @param $postcode
     * @return void
     */
    public function toggleSelection($postcode)
    {

        // If Postcode is in selected array then remove it from the list
        if (in_array($postcode, $this->properties)) {

            // Sort the filter when updating it
            $sortedProperties = array_filter($this->properties, fn($property) => $property !== $postcode);
            sort($sortedProperties);

            $this->properties = $sortedProperties;
/*
            //Remove entry from the database
            $entry = Responsibility::where('organisation', $this->organisation->id)
                ->where('postcode', $postcode)->first();

            $entry->delete();
*/
        } else {

            $this->properties[] = $postcode;

            sort($this->properties);
/*
            // Add entry to the database
            $entry = new Responsibility();
            $entry->organisation = $this->organisation->id;
            $entry->postcode = $postcode;
            $entry->save();
*/
        }
    }


    /* Return the distinct postcode list after applying the filters */
    public function render()
    {

        $selectedProperties = Property::query();

        if($this->selectedArea) {
            $selectedProperties->where('PostcodeArea', $this->selectedArea);
        }

        if($this->selectedOutcode) {
            $selectedProperties->where('Outcode', $this->selectedOutcode);
        }

        if($this->selectedIncode) {
            $selectedProperties->where('Incode', $this->selectedIncode);
        }

        if(!empty($this->properties)) {
            //$selectedProperties->whereNotIn('postcode', $this->properties->pluck('id')->toArray());
            array_diff($selectedProperties->pluck('postcode')->unique()->toArray(), $this->properties, ['']);
        }

        // Filter the array and sort it before returning it
        $sortedProperties = array_diff($selectedProperties->pluck('postcode')->unique()->toArray(), $this->properties, ['']);

        sort($sortedProperties);

        return view('livewire.postcode-dropdown', [
            'selectedProperties' => $sortedProperties,
        ]);
    }
}
