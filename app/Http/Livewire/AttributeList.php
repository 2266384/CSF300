<?php

namespace App\Http\Livewire;


use App\Models\Customer;
use App\Models\NeedCode;
use App\Models\ServiceCode;
use Illuminate\Support\Arr;
use Livewire\Component;
use App\Services\AttributeService;
use Livewire\Features\SupportAttributes\AttributeCollection;

class AttributeList extends Component
{
    // List of items
    public $items = [];

    // Array to track selected items (stores their indices or IDs)
    public $selectedItems = [];
    public $deselectedItems = [];

    // Array to track items which should have disabled status
    public $disabledItems = [];

    public $customer;   // Optional parameter

    // Service injection for Attribute Service
    protected $attributeService;
    public $currentAttributes = [];           // array to hold the attributes


    /**
     * Method for initialising the lists
     * @param Customer $customer
     * @return void
     */
    public function mount(Customer $customer = null, AttributeService $attributeService)
    {
        $this->customer = $customer ?? 'default';
        $this->attributeService = $attributeService;

        // If the customer is not set to default then load the current attributes
        if($this->customer !== 'default') {

            $this->loadAttributes();

            // Get the attributes currently assigned to the customer
            //$this->currentAttributes($customer);
        }

        // Get all the attributes
        //$this->items = $this->indexedAttributes();
        $this->items = $this->attributeService->indexedAttributes();

        // Set all the default disabled items
        $this->defaultDisabledItems();
    }


    /**
     * Call the Service to load the current attribute for the customer
     * @return void
     */
    public function loadAttributes() {
        $this->currentAttributes = $this->attributeService->currentAttributes($this->customer);

        // Add the current attributes to the selectedItems array
        foreach ($this->currentAttributes as $item) {
            $this->selectedItems[] = $item['index'];
            $this->disabledItems[] = $item['index'];
        }
    }



    /**
     * Method to move the selected item between the lists
     * @param $itemId
     * @return void
     */
    public function toggleSelection($itemId)
    {
        if (in_array($itemId, $this->selectedItems)) {
            //dd($itemId);
            $this->selectedItems = array_filter($this->selectedItems, fn($id) => $id !== $itemId);
            $this->deselectedItems[] = $itemId;
            $this->toggleDisabledItems($itemId);
        } else {
            $this->selectedItems[] = $itemId;
            $this->toggleDisabledItems($itemId);
        }

        $this->checkDisabledItems();

        /*
         * Sometimes the disabled items check removes the selected code from the
         * array of selected items - check if its been removed and re-add it
         */
        foreach($this->selectedItems as $itemId) {
            if(!in_array($itemId, $this->disabledItems)) {
                $this->disabledItems[] = $itemId;
            }
        }

        if(!empty($this->deselectedItems)) {

            $printSizeSelected = false;
            $printColourSelected = false;

            $printCodes = [
                ['16P', '20P', '24P'],                      // Print Size
                ['CLB', 'CDB', 'CRE', 'CYE'],               // Print colours
            ];
            $audioCodes = ['FBR', 'FCD', 'FMP', 'FPH'];     // Audio Formats

            // Check if we still have any of these types selected
            if(array_intersect($this->items->whereIn('code', $printCodes[0])
                ->pluck('index')->toArray(), $this->selectedItems)) {
                $printSizeSelected = true;
            }

            if(array_intersect($this->items->whereIn('code', $printCodes[1])
                ->pluck('index')->toArray(), $this->selectedItems)) {
                $printColourSelected = true;
            }

            if(array_intersect($this->items->whereIn('code', $audioCodes)
                ->pluck('index')->toArray(), $this->selectedItems)) {
                $audioFormatSelected = true;
            }


            /*
             * Check if we've removed a print size from the selection and make sure
             * to remove the other options from the disabled list
             */
            foreach($printCodes as $printCode) {

                // Get the indexes
                $result = $this->items->whereIn('code', $printCode)->pluck('index');

                // Check to see if we've deselected any of the print formats
                if (array_intersect($result->toArray(), $this->deselectedItems)) {
                    foreach($result as $itemId) {
                        $this->disabledItems = array_filter($this->disabledItems, fn($id) => $id !== $itemId);
                    }
                }
            }


            /*
             * Combine all the print formats and check if we've removed both
             * size and colour options and re-enable the audio formats if they're still
             * disabled
             */
            $compiledPrint = Arr::flatten($printCodes);

            $result = $this->items->whereIn('code', $compiledPrint)->pluck('index');

            // Check if we don't have any print formats left
            if (!array_intersect($result->toArray(), $this->selectedItems)) {
                // Get the indexes for the audio formats
                $result = $this->items->whereIn('code', $audioCodes)->pluck('index');

                foreach($result as $itemId) {
                    $this->disabledItems = array_filter($this->disabledItems, fn($id) => $id !== $itemId);
                }
            }

            /*
             * Check if we've removed the audio formats from the select list
             * and re-enable the print formats if this is the case
             */
            $result = $this->items->whereIn('code', $audioCodes)->pluck('index');

            //dd($printSizeSelected, $printColourSelected, $audioFormatSelected);
            // Check if we don't have any audio formats left
            if (
                (!array_intersect($result->toArray(), $this->selectedItems) &&
                !$printSizeSelected && !$printColourSelected) ||
                array_intersect($this->items->whereIn('code', ['9'])
                    ->pluck('index')->toArray(),$this->deselectedItems)
            ) {

                if(array_intersect($this->items->whereIn('code', ['9'])
                    ->pluck('index')->toArray(),$this->selectedItems)) {

                    // Get the indexes for the print formats
                    $result = $this->items->whereIn('code', $compiledPrint)->pluck('index');

                } else if (array_intersect($this->items->whereIn('code', ['8'])
                    ->pluck('index')->toArray(),$this->selectedItems)
                ) {
                    $result = $this->items->whereIn('code', $audioCodes)->pluck('index');
                }

                foreach($result as $itemId) {
                    $this->disabledItems = array_filter($this->disabledItems, fn($id) => $id !== $itemId);
                }

            }

            /*
             * Check if we've disabled Blind or Partially Sighted and make sure to reset the
             * print and audio options
            */

            // Partially sighted
            if (array_intersect($this->items->whereIn('code', ['9'])
                ->pluck('index')->toArray(),$this->deselectedItems)) {

                //dd($printSizeSelected, $printColourSelected);

                // Remove any selected print and audio formats
                $allBillFormats = array_merge($compiledPrint, $audioCodes);
                $result = $this->items->whereIn('code', $allBillFormats)->pluck('index');

                foreach($result as $itemId) {

                    if (in_array($itemId, $this->selectedItems)) {
                        $this->selectedItems = array_filter($this->selectedItems, fn($id) => $id !== $itemId);
                    }

                    if(!in_array($itemId, $this->disabledItems)) {
                        $this->toggleDisabledItems($itemId);
                    }
                }

            }

            // Partially sighted
            if (array_intersect($this->items->whereIn('code', ['8'])
                ->pluck('index')->toArray(),$this->deselectedItems)) {

                //dd($printSizeSelected, $printColourSelected);

                // Remove any selected audio formats
                $result = $this->items->whereIn('code', $audioCodes)->pluck('index');

                foreach($result as $itemId) {

                    if (in_array($itemId, $this->selectedItems)) {
                        $this->selectedItems = array_filter($this->selectedItems, fn($id) => $id !== $itemId);
                    }

                    if(!in_array($itemId, $this->disabledItems)) {
                        $this->toggleDisabledItems($itemId);
                    }
                }

            }

            // Clear the deselected items
            $this->deselectedItems = [];

        }

        // Check if the selected items array is empty and set the default disabled items
        if (empty($this->selectedItems)) {
            $this->disabledItems = [];
            $this->defaultDisabledItems();
        }

        // Re-index the array to keep the indices sequential
        $this->selectedItems = array_values($this->selectedItems);
    }


    private function toggleDisabledItems($itemId) {

        $item = $this->items->where('index', $itemId)->first();
        $type = $item['type'];
        //dd($item);

        if ($type === 'need') {
            // If item is need remove it from the disabled list, else add it
            if (in_array($itemId, $this->disabledItems)) {
                $this->disabledItems = array_filter($this->disabledItems, fn($id) => $id !== $itemId);
            } else {
                $this->disabledItems[] = $itemId;
            }
            // If item is a service add it to the disabled list, else remove it
        } else if ($type === 'service') {
            if (!in_array($itemId, $this->disabledItems)) {
                $this->disabledItems[] = $itemId;
                //dd($item, $this->disabledItems);
            } else {
                $this->disabledItems = array_filter($this->disabledItems, fn($id) => $id !== $itemId);
            }
        }
    }


    /**
     * Set default disabled items - all Services
     */
    function defaultDisabledItems() {
        // Check through the selectedItems List
        foreach ($this->items as $item) {

            // Get the matching item from the items list
            if ( $item['type'] == 'service'  && !in_array($item['code'], ['PAS', 'NOM'])) {
                $this->disabledItems[] = $item['index'];
            }
        }
    }


    function checkDisabledItems() {

        //$item = $this->items->where('index', $itemID)->first();

        // List of need codes and codes which should be enabled and disabled
        $codeList = collect([
            ['code' => '8', 'enabled' => ['FBR', 'FCD', 'FMP', 'FPH'], 'disabled' => ['9']],
            ['code' => '9', 'enabled' => ['FBR', 'FCD', 'FMP', 'FPH', '16P', '20P', '24P', 'CLB', 'CDB', 'CRE', 'CYE'], 'disabled' => ['8']],
            ['code' => '18', 'enabled' => ['FBR', 'FCD', 'FMP', 'FPH', '16P', '20P', '24P', 'CLB', 'CDB', 'CRE', 'CYE'], 'disabled' => []],
            ['code' => '30', 'enabled' => ['FBR', 'FCD', 'FMP', 'FPH', '16P', '20P', '24P', 'CLB', 'CDB', 'CRE', 'CYE'], 'disabled' => []],
            ['code' => '35', 'enabled' => ['TXT']],
            ['code' => '36', 'enabled' => ['TXT']],
            ['code' => 'FBR', 'enabled' => [], 'disabled' => ['FCD', 'FMP', 'FPH', '16P', '20P', '24P', 'CLB', 'CDB', 'CRE', 'CYE']],
            ['code' => 'FCD', 'enabled' => [], 'disabled' => ['FBR', 'FMP', 'FPH', '16P', '20P', '24P', 'CLB', 'CDB', 'CRE', 'CYE']],
            ['code' => 'FMP', 'enabled' => [], 'disabled' => ['FCD', 'FBR', 'FPH', '16P', '20P', '24P', 'CLB', 'CDB', 'CRE', 'CYE']],
            ['code' => 'FPH', 'enabled' => [], 'disabled' => ['FCD', 'FBR', 'FMP', '16P', '20P', '24P', 'CLB', 'CDB', 'CRE', 'CYE']],
            ['code' => '16P', 'enabled' => [], 'disabled' => ['FBR', 'FCD', 'FMP', 'FPH', '20P', '24P']],
            ['code' => '20P', 'enabled' => [], 'disabled' => ['FBR', 'FCD', 'FMP', 'FPH', '16P', '24P']],
            ['code' => '24P', 'enabled' => [], 'disabled' => ['FBR', 'FCD', 'FMP', 'FPH', '16P', '20P']],
            ['code' => 'CLB', 'enabled' => [], 'disabled' => ['FBR', 'FCD', 'FMP', 'FPH', 'CDB', 'CRE', 'CYE']],
            ['code' => 'CDB', 'enabled' => [], 'disabled' => ['FBR', 'FCD', 'FMP', 'FPH', 'CLB', 'CRE', 'CYE']],
            ['code' => 'CRE', 'enabled' => [], 'disabled' => ['FBR', 'FCD', 'FMP', 'FPH', 'CLB', 'CDB', 'CYE']],
            ['code' => 'CYE', 'enabled' => [], 'disabled' => ['FBR', 'FCD', 'FMP', 'FPH', 'CLB', 'CDB', 'CRE']],
        ]);

        $enabledItems = [];
        $disabledItems = [];

        // Check all the selected items and compile a list of all enabled and disabled codes
        foreach ($this->selectedItems as $selected) {

            //dd($selected);

            $codeList->map(function ($item) use (&$enabledItems, &$disabledItems, $selected) {
                $selectedCode = $this->items->where('index', $selected)->first();
               if($item['code'] === $selectedCode['code']) {

                   if(!empty($item['enabled'])) {
                       foreach ($item['enabled'] as $i) {
                           $att = $this->items->where('code', $i)->first();
                           //if(!in_array($att, $this->selectedItems)) {
                               //dd($selectedCode, $i, $att, $this->selectedItems);
                               $enabledItems[] = $att['index'];
                           //}
                       }
                   }

                   if(!empty($item['disabled'])) {
                       foreach ($item['disabled'] as $i) {
                           $att = $this->items->where('code', $i)->first();
                           //if(in_array($att['index'], $this->selectedItems)) {
                           //dd($selectedCode, $i, $att, $this->selectedItems);
                                $disabledItems[] = $att['index'];
                           //}
                       }
                   }

               }
            });

        }

        // Convert the arrays to unique lists
        $uniqueEnabled = array_unique($enabledItems);
        $uniqueDisabled = array_unique($disabledItems);

        //dd($uniqueEnabled, $uniqueDisabled);

        // Remove any enabled items that exist in the disabled items list
        $toEnable = array_diff($uniqueEnabled, $uniqueDisabled);

        //dd($toEnable);

        //Iterate through the disabled items and add them to the list if they don't exist
        foreach ($uniqueDisabled as $item) {
            if (!in_array($item, $this->disabledItems)) {
                $this->disabledItems[] = $item;
            }
        }

        // Iterate through the enabled items and remove them from the disabled items list if they exist
        foreach ($toEnable as $item) {
            if (in_array($item, $this->disabledItems)) {
                $this->disabledItems = array_filter($this->disabledItems, fn($id) => $id !== $item);
            }
        };

        //dd($this->disabledItems, $this->items);

        // Check for a deselected item and add/remove values
        if(!empty($this->deselectedItems)) {

            foreach ($this->deselectedItems as $item) {
                $deselected = $this->items->where('index', $item)->first();
                $code = $deselected['code'];
                $deselectedCode = $codeList->where('code', $code);
                //dd($deselectedCode, $code, $type);

                foreach ($deselectedCode as $deselected) {

                    // Go through enabled attributes and remove them
                    if(!empty($deselected['enabled'])) {

                        foreach($deselected['enabled'] as $d) {

                            $deselectedItem = $this->items->where('code', $d)->first();
                            $deselectedIndex = $deselectedItem['index'];
                            $deselectedType = $deselectedItem['type'];

                            //dd($deselectedItem, $deselectedIndex, $deselectedType);

                            if (
                                // Check if the index exists in the selected items list AND the disabled items list
                                in_array($deselectedIndex, $this->selectedItems)
                                && in_array($deselectedIndex, $this->disabledItems)
                            ) {
                                // Do Nothing - this is to be expected
                            } else if (
                                // Index exists in the selected items list BUT not in the disabled items list
                                in_array($deselectedIndex, $this->selectedItems)
                                && !in_array($deselectedIndex, $this->disabledItems)
                            ) {
                                // Add to the disabled list - this should not happen with deselected items
                                $this->disabledItems[] = $deselectedIndex;
                            } else if (
                                // Index is not in the selected items list AND is in the disabled items list
                                !in_array($deselectedIndex, $this->selectedItems) &&
                                in_array($deselectedIndex, $this->disabledItems)
                            ) {
                                if ( $deselectedType == 'need' ) {
                                    // If this is a need it should be removed from the disabled items list
                                    $this->disabledItems = array_filter($this->disabledItems, fn($id) => $id !== $deselectedIndex);
                                } else if ( $deselectedType == 'service' ) {
                                    // If this is a Service it should be added to the disabled items list
                                    $this->disabledItems[] = $deselectedIndex;
                                }
                            } else if (
                                // Index is not in the selected items list AND is not in the disabled items list
                                !in_array($deselectedIndex, $this->selectedItems) &&
                                !in_array($deselectedIndex, $this->disabledItems)
                            ) {
                                if ( $deselectedType == 'need' ) {
                                    // Do nothing
                                } else if ( $deselectedType == 'service' ) {
                                    // If this is a Service it should be added to the disabled items list
                                    $this->disabledItems[] = $deselectedIndex;
                                }
                            }

                        };
                    }

                    if (!empty($deselected['disabled'])) {

                        // Remove the enabled attributes
                        foreach($deselected['disabled'] as $d) {

                            $deselectedItem = $this->items->where('code', $d)->first();
                            $deselectedIndex = $deselectedItem['index'];
                            $deselectedType = $deselectedItem['type'];

                            //dd($deselectedItem, $deselectedIndex, $this->selectedItems, $this->disabledItems);

                            if (
                                // Check if the index exists in the selected items list AND the disabled items list
                                in_array($deselectedIndex, $this->selectedItems)
                                && in_array($deselectedIndex, $this->disabledItems)
                            ) {
                                // Do Nothing - this is to be expected
                            } else if (
                                // Index exists in the selected items list BUT not in the disabled items list
                                in_array($deselectedIndex, $this->selectedItems)
                                && !in_array($deselectedIndex, $this->disabledItems)
                            ) {
                                // Add to the disabled list - this should not happen with deselected items
                                $this->disabledItems[] = $deselectedIndex;
                            } else if (
                                // Index is not in the selected items list AND is in the disabled items list
                                !in_array($deselectedIndex, $this->selectedItems) &&
                                in_array($deselectedIndex, $this->disabledItems)
                            ) {
                                if ( $deselectedType == 'need' ) {
                                    // If this is a need it should be removed from the disabled items list
                                    $this->disabledItems = array_filter($this->disabledItems, fn($id) => $id !== $deselectedIndex);
                                } else if ( $deselectedType == 'service' ) {
                                    // If this is a Service it should be added to the disabled items list
                                    $this->disabledItems[] = $deselectedIndex;
                                }
                            } else if (
                                // Index is not in the selected items list AND is not in the disabled items list
                                !in_array($deselectedIndex, $this->selectedItems) &&
                                !in_array($deselectedIndex, $this->disabledItems)
                            ) {
                                if ( $deselectedType == 'need' ) {
                                    // Do nothing
                                } else if ( $deselectedType == 'service' ) {
                                    // If this is a Service it should be added to the disabled items list
                                    $this->disabledItems[] = $deselectedIndex;
                                }
                            }

                        };//dd($deselectedIndex, $code, $type);
                    }
                }
            }
        }


        // Check if the selected items array is empty and set the default disabled items
        if (empty($this->selectedItems)) {
            $this->disabledItems = [];
            $this->defaultDisabledItems();
        }

        // Clear the deselected items array
        //$this->deselectedItems = [];




    }




    /**
     * Render the component
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function render()
    {
        return view('livewire.attribute-list', ['customer' => $this->customer]);
    }
}
