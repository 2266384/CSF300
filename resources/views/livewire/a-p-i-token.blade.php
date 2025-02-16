<div class="col-md-6">

    <div class="d-flex align-items-center">
        <label for="representativeapitoken">API Token:</label>
        <span id="api-token-error" class="error text-danger">@error('api-token') {{ $message }} @enderror</span>
    </div>
    <div class="input-group mb-2">
        <input id="representativeapitoken" name="api-token" type="text" class="form-control"
               value="{{ $token }}"
               aria-label="Representative API Token" aria-describedby="api-token-button" >
        <button class="btn btn-outline-secondary" type="button" id="api-token-button" wire:click="createToken">New API Token</button>
    </div>
    <div>
        <em>New tokens will automatically be added to the clipboard - once submitted you will not be able to retrieve this value</em>
    </div>

</div>
