
<div>

    <div class="dropdowns d-flex align-items-center">

        <div class="mt-2 col-md-3">
            <label for="postcode-area">Postcode Area</label>
            <select wire:model.live="selectedArea"
                class="text-sm" name="postcode-area">
                <option value="" selected>Choose Postcode Area</option>
                @foreach($areas as $area)
                    <option value="{{ $area }}">{{ $area }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-2 col-md-3">
            <label for="outcode">Outcode</label>
            <select wire:model.live="selectedOutcode"
                    class="text-sm" name="outcode">
                <option value="" selected>Choose Outcode</option>
                @if($outcodes)
                    @foreach($outcodes as $outcode)
                        <option value="{{ $outcode }}">{{ $outcode }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="mt-2 col-md-3">
            <label for="incode">Incode</label>
            <select wire:model.live="selectedIncode"
                    class="text-sm" name="incode">
                <option value="" selected>Choose Incode</option>
                @if($incodes)
                    @foreach($incodes as $incode)
                        <option value="{{ $incode }}">{{ $incode }}</option>
                    @endforeach
                @endif
            </select>
        </div>

    </div>

    <div class="postcodes d-flex align-items-center">

        <div class="col-md-5 mr-4">
            <label for="filtered-properties">Available Postcodes:</label>
            <select id="filtered-properties" name="filtered-properties" class="form-select" size="20" aria-label="Filtered Property List">
                @foreach($selectedProperties as $property)
                    <option wire:click="toggleSelection('{{ $property }}')" value="{{ $property }}">
                        {{ $property }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-5 mx-4">
            <label for="current-properties">Current Postcodes:</label>
            <select id="current-properties" name="current-properties" class="form-select" size="20" aria-label="Current Property List">
                @foreach($properties as $property)
                    <option wire:click="toggleSelection('{{ $property }}')" value="{{ $property }}">
                        {{ $property }}</option>
                @endforeach
            </select>
        </div>

    </div>

</div>
