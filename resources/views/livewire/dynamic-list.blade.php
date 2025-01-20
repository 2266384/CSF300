<div>
    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <!-- Input to control the disabled state -->
    <label for="inputControl">Enable/Disable Items</label>
    <!-- <select id="inputControl" wire:model="selectedStatus"> -->
    <select id="inputControl" wire:change="updateList($event.target.value)">
        <option value="">Select an Option</option>
        <option value="disable_even">Disable Even Items</option>
        <option value="disable_odd">Disable Odd Items</option>
        <option value="enable_all">Enable All</option>
    </select>

    <hr>

    <!-- Dynamic List -->
    <ul>
        @foreach($items as $index => $item)
            <li>
                <button
                        wire:click="handleItemClick({{ $index }})"
                        @if($item['disabled']) disabled @endif
                >
                    {{ $item['name'] }}
                </button>
            </li>
        @endforeach
    </ul>
</div>
