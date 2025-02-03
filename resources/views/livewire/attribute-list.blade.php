
<div class="container">
    <div class="row">
        <!-- List of available source items -->
        <div class="form-group col-md-6">
            <form id="available-attributes">
                @csrf
                <label for="available-attribute-list">Available Needs and Services:</label>
                <select id="available-attribute-list" name="selected_attribute" class="form-select" size="20" aria-label="Source Attributes List">
                    @foreach($items as $item)
                        @php
                            $selectedItem = collect($items)->firstWhere('index', $item['index']);
                        @endphp
                        <option wire:click="toggleSelection({{ $item['index'] }})" value="{{ $item["code"] }}"
                                data-title="{{ $item['code'] }}"
                                data-description="{{ $item['description'] }}"
                                @if ( in_array($item['index'],$disabledItems) ) disabled @endif
                                @if ( in_array($item['code'], ['32', '33', '34']) ) data-title="{{ $item["code"] }}" data-description="{{ $item["description"] }}" data-bs-toggle="modal" data-bs-target="#dateModal" @endif>
                            {{ $item["description"] }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <!--- List of selected items -->
        <div class="form-group col-md-6">
            <form id="current-attributes">
                @csrf
                <label for="current-attributes-list">Selected Needs and Services:</label>
                <span id="arrayData-error" class="error text-danger">@error('arrayData') {{ $message }} @enderror</span>
                <select id="current-attributes-list" size="20" name="arrayData" aria-label="Selected Attributes List" required class="form-select {{ $errors->has('arrayData_name') ? 'is-invalid' : '' }}">
                    @foreach($selectedItems as $selectedItemId)
                        @php
                            $selectedItem = collect($items)->firstWhere('index', $selectedItemId);
                        @endphp
                        <option wire:click="toggleSelection({{ $selectedItem['index'] }})" value="{{ json_encode([
                                    "sort" => $selectedItem["index"],
                                    "code" => $selectedItem["code"],
                                    "type" => $selectedItem["type"]
                                    ]) }}">{{ $selectedItem["description"] }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Modal - popup to capture date-->
    <div class="modal fade" id="dateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="dateModalTitle">{{ $selectedItem['description'] }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="expire">Expiry Date:</label>
                    <input type="date" id="expire" name="expire_date" min="2025-01-01">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelDate" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveDate" data-bs-dismiss="modal" disabled>Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Temporary Need Dates - use wire:ignore to stop the readonly state from being reset when the livewire component re-renders-->
    <div class="container" id="tempdates">
        <div class="row">
            <div class="col-md-4">
                <!-- Error message container -->
                <label for="tlc-picker">Temporary - Life Changes:</label>
                <span id="tlc_date-error" class="error text-danger">@error('tlc_date_name') {{ $message }} @enderror</span>
                @php
                        $attributes = array_filter($currentAttributes, function ($att) {
                        return $att['description'] === 'Temporary - Life changes';
                    });
                    $filtered = !empty($attributes) ? reset($attributes) : null;
                @endphp
                @if($filtered)
                    <!-- Get the selected item from the model -->
                    @if( !is_null($filtered['temp_end_date']) )
                        <input type="date" class="form-control-plaintext {{ $errors->has('tlc_date_name') ? 'is-invalid' : '' }}" id="tlc-picker" name="tlc_date" value="{{ $filtered['temp_end_date'] }}" wire:ignore>
                    @else
                        <input type="date" readonly class="form-control-plaintext {{ $errors->has('tlc_date_name') ? 'is-invalid' : '' }}" id="tlc-picker" name="tlc_date" value="" wire:ignore>
                    @endif
                @else
                    <input type="date" readonly class="form-control-plaintext {{ $errors->has('tlc_date_name') ? 'is-invalid' : '' }}" id="tlc-picker" name="tlc_date" value="" wire:ignore>
                @endif
            </div>
            <div class="col-md-4">
                <!-- Error message container -->
                <label for="phr-picker">Temporary - Post Hospital Recovery:</label>
                <span id="phr_date-error" class="error text-danger">@error('phr_date_name') {{ $message }} @enderror</span>
                @php
                    $attributes = array_filter($currentAttributes, function ($att) {
                        return $att['description'] === 'Temporary - Post hospital recovery';
                    });
                    $filtered = !empty($attributes) ? reset($attributes) : null;
                @endphp
                @if($filtered)
                    <!-- Get the selected item from the model -->
                    @if( !is_null($filtered['temp_end_date']) )
                        <input type="date" class="form-control-plaintext {{ $errors->has('phr_date_name') ? 'is-invalid' : '' }}" id="phr-picker" name="phr_date" value="{{ $filtered['temp_end_date'] }}" wire:ignore>
                    @else
                        <input type="date" readonly class="form-control-plaintext {{ $errors->has('phr_date_name') ? 'is-invalid' : '' }}" id="phr-picker" name="phr_date" value="" wire:ignore>
                    @endif
                @else
                    <input type="date" readonly class="form-control-plaintext {{ $errors->has('phr_date_name') ? 'is-invalid' : '' }}" id="phr-picker" name="phr_date" value="" wire:ignore>
                @endif
            </div>
            <div class="col-md-4">
                <!-- Error message container -->
                <label for="yah-picker">Temporary - Young Adult Householder:</label>
                <span id="yah_date-error" class="error text-danger">@error('yah_date_name') {{ $message }} @enderror</span>
                @php
                    $attributes = array_filter($currentAttributes, function ($att) {
                        return $att['description'] === "Temporary - Young adult householder(<18)";
                    });
                    $filtered = !empty($attributes) ? reset($attributes) : null;
                @endphp
                @if($filtered)
                    <!-- Get the selected item from the model -->
                    @if( !is_null($filtered['temp_end_date']) )
                        <input type="date" class="form-control-plaintext {{ $errors->has('yah_date_name') ? 'is-invalid' : '' }}" id="yah-picker" name="yah_date" value="{{ $filtered['temp_end_date'] }}" wire:ignore>
                    @else
                        <input type="date" readonly class="form-control-plaintext {{ $errors->has('yah_date_name') ? 'is-invalid' : '' }}" id="yah-picker" name="yah_date" value="" wire:ignore>
                    @endif
                @else
                    <input type="date" readonly class="form-control-plaintext {{ $errors->has('yah_date_name') ? 'is-invalid' : '' }}" id="yah-picker" name="yah_date" value="" wire:ignore>
                @endif
            </div>
        </div>
    </div>

</div>


<script>
    // Set the min attribute of the date pickers to today's date
    document.getElementById('tlc-picker').setAttribute('min', minDate);
    document.getElementById('phr-picker').setAttribute('min', minDate);
    document.getElementById('yah-picker').setAttribute('min', minDate);


<!-- Dynamically add the script to the date picker modal -->
    document.addEventListener('DOMContentLoaded', function() {
        const cancelDateBtn = document.getElementById('cancelDate');
        const saveDateBtn = document.getElementById('saveDate');
        const expireInput = document.getElementById('expire');
        const tlcPicker = document.getElementById('tlc-picker');
        const phrPicker = document.getElementById('phr-picker');
        const yahPicker = document.getElementById('yah-picker');
        const selectList = document.getElementById('available-attribute-list');
        const currentList = document.getElementById('current-attributes-list');
        const modal = document.getElementById('dateModal')

        let action = null;          // Action to be undertaken
        let currentOption = null;   // Reference to the currently selected option
        let dateField = null;       // Variable to hold the date picker

/*
        // Add event listener to the select list
        selectList.addEventListener('change', (event) => {
            const selectedOption = event.target.options[event.target.selectedIndex];

            // Check if selected option has a data toggle
            if (selectedOption.hasAttribute('data-bs-toggle') && selectedOption.getAttribute('data-bs-toggle') === 'modal') {
                const title = selectedOption.getAttribute('data-title');
                const description = selectedOption.getAttribute('data-description');

                expireInput.value = '';
                saveDateBtn.disabled = true;

                expireInput.addEventListener('input', function onInput() {
                    saveDateBtn.disabled = !expireInput.value;
                }, {once: true});
            }

            //const modalInstance = new bootstrap.Modal(modal);
            //modalInstance.show();

        });

 */

        // Function to return the correct date field
        function getDateField(option) {
            // Choose the date display based on the chosen option
            switch(option) {
                case "Temporary - Life changes":
                    return tlcPicker;
                    break;
                case "Temporary - Post hospital recovery":
                    return phrPicker;
                    break;
                case "Temporary - Young adult householder(<18)":
                    return yahPicker;
                    break;
            }

        }

        // Event handler for select list to get the date picker for population
        selectList.addEventListener('click', function () {

            // Set the minimum date on the date picker to today
            expireInput.addEventListener('click', () => {
                expireInput.setAttribute('min', minDate);
            });

            // Check to see if the date field has a value and enable the save button
            expireInput.addEventListener('input', () => {


                if (expireInput.value) {
                    saveDateBtn.disabled = false;
                } else {
                    saveDateBtn.disabled = true;
                }
            });



            // If we select the Cancel button trigger a select of the attribute on the currentlist so it returns
            cancelDateBtn.addEventListener('click', () => {

                currentOption = selectList.options[selectList.selectedIndex]
                // console.log(currentOption);

                let opt = currentOption.value;

                // Loop through each option in the select list and find the one we've just selected
                for (let i = 0; i < currentList.options.length; i++) {
                    const option = currentList.options[i];

                    // Match the code values
                    if(JSON.parse(option.value).code === opt) {

                        // Find the LiveWire click attribute
                        const hasWireClick = option.hasAttribute('wire:click');

                        // If the option has a LiveWire Click event then trigger the event
                        if (hasWireClick) {
                            //console.log(`wire:click value: ${option.getAttribute('wire:click')}`);
                            action = 'cancel';
                            option.click();
                        }

                    }
                }

            })


            currentOption = selectList.options[selectList.selectedIndex]; // Get the selected <option>
            dateField = getDateField(currentOption.text);
            //action = 'add';
        });


        // Event handler for current list to get the date picker to clear
        currentList.addEventListener('click', function() {

            // Only clear the dates if we haven't clicked cancel
            if(action !== 'cancel') {

                currentOption = currentList.options[currentList.selectedIndex]; // Get the selected <option>
                dateField = getDateField(currentOption.text);

                dateField.value = "";
            }

            // Clear the action variable again
            action = null;

        })


        // Save the selected date for the current option
        saveDateBtn.addEventListener('click', function () {

            dateField.value = expireInput.value;
            dateField.readOnly = false;

            // Clear the value from the date picker
            expireInput.value = "";

        });

    });

</script>



