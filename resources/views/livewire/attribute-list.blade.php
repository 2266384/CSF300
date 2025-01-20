
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
                    <input type="date" id="expire" name="expire_date">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveDate" data-bs-dismiss="modal" disabled>Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container" id="tempdates">
        <div class="row">
            <div class="col-md-4">
                <!-- Error message container -->
                <span id="tlc_date-error" class="error text-danger">@error('tlc_date_name') {{ $message }} @enderror</span>
                <label for="tlc">Temporary - Life Changes:</label>
                @php
                        $attributes = array_filter($currentAttributes, function ($att) {
                        return $att['description'] === 'Temporary - Life changes';
                    });
                    $filtered = !empty($attributes) ? reset($attributes) : null;
                @endphp
                @if($filtered)
                    <!-- Get the selected item from the model -->
                    @if( !is_null($filtered['temp_end_date']) )
                        <input type="date" class="form-control-plaintext" id="tlc" name="tlc_date" value="{{ $filtered['temp_end_date'] }}">
                    @else
                        <input type="date" readonly class="form-control-plaintext" id="tlc" name="tlc_date" value="">
                    @endif
                @else
                    <input type="date" readonly class="form-control-plaintext" id="tlc" name="tlc_date" value="">
                @endif
            </div>
            <div class="col-md-4">
                <!-- Error message container -->
                <span id="phr_date-error" class="error text-danger">@error('phr_date_name') {{ $message }} @enderror</span>
                <label for="phr">Temporary - Post Hospital Recovery:</label>
                @php
                    $attributes = array_filter($currentAttributes, function ($att) {
                        return $att['description'] === 'Temporary - Post hospital recovery';
                    });
                    $filtered = !empty($attributes) ? reset($attributes) : null;
                @endphp
                @if($filtered)
                    <!-- Get the selected item from the model -->
                    @if( !is_null($filtered['temp_end_date']) )
                        <input type="date" class="form-control-plaintext" id="phr" name="phr_date" value="{{ $filtered['temp_end_date'] }}">
                    @else
                        <input type="date" readonly class="form-control-plaintext" id="phr" name="phr_date" value="">
                    @endif
                @else
                    <input type="date" readonly class="form-control-plaintext" id="phr" name="phr_date" value="">
                @endif
            </div>
            <div class="col-md-4">
                <!-- Error message container -->
                <span id="yah_date-error" class="error text-danger">@error('yah_date_name') {{ $message }} @enderror</span>
                <label for="yah">Temporary - Young Adult Householder:</label>
                @php
                    $attributes = array_filter($currentAttributes, function ($att) {
                        return $att['description'] === "Temporary - Young adult householder(<18)";
                    });
                    $filtered = !empty($attributes) ? reset($attributes) : null;
                @endphp
                @if($filtered)
                    <!-- Get the selected item from the model -->
                    @if( !is_null($filtered['temp_end_date']) )
                        <input type="date" class="form-control-plaintext" id="yah" name="yah_date" value="{{ $filtered['temp_end_date'] }}">
                    @else
                        <input type="date" readonly class="form-control-plaintext" id="yah" name="yah_date" value="">
                    @endif
                @else
                    <input type="date" readonly class="form-control-plaintext" id="yah" name="yah_date" value="">
                @endif
            </div>
        </div>
    </div>

</div>

<!-- Dynamically add the script to the date picker modal -->
<script>

    document.addEventListener('DOMContentLoaded', function() {
        const saveDateBtn = document.getElementById('saveDate');
        const expireInput = document.getElementById('expire');
        const tlcPicker = document.getElementById('tlc');
        const phrPicker = document.getElementById('phr');
        const yahPicker = document.getElementById('yah');
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

            // Check to see if the date field has a value and enable the save button
            expireInput.addEventListener('input', () => {
                if (expireInput.value) {
                    saveDateBtn.disabled = false;
                } else {
                    saveDateBtn.disabled = true;
                }
            });

            currentOption = selectList.options[selectList.selectedIndex]; // Get the selected <option>
            dateField = getDateField(currentOption.text);
            action = 'add';
        });

        // Event handler for current list to get the date picker to clear
        currentList.addEventListener('click', function() {
            currentOption = currentList.options[currentList.selectedIndex]; // Get the selected <option>
            dateField = getDateField(currentOption.text);

            dateField.value = "";
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



