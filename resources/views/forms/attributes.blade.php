
<div class="container">
    <div class="row">
        <!-- List of available source items -->
        <div class="form-group col-md-5">
            <form id="available-attributes">
                @csrf
                <label for="available-attribute-list">Available Needs and Services:</label>
                <select id="available-attribute-list" name="selected_attribute" class="form-select" size="20" aria-label="Source Attributes List">
                @foreach($attributesservice->getAvailableAttributes($customer) as $attribute)
                    <option value="{{ json_encode([
                                    "sort" => $attribute["index"],
                                    "code" => $attribute["code"],
                                    "type" => $attribute["type"],
                                    "state" => $attribute["state"],
                                    "action" => "add"]) }}"
                                    @if ($attribute['state'] == 'disabled')
                                    disabled
                        @endif >{{ $attribute['description'] }}</option>
                @endforeach
            </select>
            </form>
        </div>
        <div class="gap-2 col-md-2 attribute-controls">
            <button type="submit" id="remove-attribute" class="btn col-md-5 btn-primary"><<</button>
            <button type="submit" id="add-attribute" class="btn col-md-5 btn-primary">>></button>
        </div>
        <!--- List of selected items -->
        <div class="form-group col-md-5">
            <form id="current-attributes">
                @csrf
                <label for="current-attributes-list">Selected Needs and Services:</label>
                <select id="current-attributes-list" class="form-select" size="20" aria-label="Selected Attributes List" required>
                @foreach($attributesservice->currentAttributes($customer) as $attribute)
                    <option value="{{ json_encode([
                                    "sort" => $attribute["index"],
                                    "code" => $attribute["code"],
                                    "type" => $attribute["type"],
                                    "action" => "remove"]) }}">{{ $attribute['description'] }}</option>
                @endforeach
                </select>
            </form>
        </div>
    </div>
</div>



