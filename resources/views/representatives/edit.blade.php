<x-layout Title="Representatives">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>Edit Representative:</H2>
            </div>
            <button type="button" id="update-representative" class="btn p-0 border-0 bg-transparent" title="Update Representative"><i class="bi bi-floppy fs-2"></i></button>
            <a href="{{route('representatives.index')}}" id="cancel-save-representative" title="Cancel Update"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>


        <div>
            <form id="representative-data" method="POST" action="{{route('representatives.update', $representative)}}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-md-1">
                        <label for="representativeid">ID:</label>
                        <input type="text" id="representativeid" name="id" value="{{ $representative->id }}"
                               class="form-control-plaintext">
                    </div>

                    <div class="form-group col-sm-1 checkbox">
                        <input type="hidden" name="active" value="0">
                        <input class="form-check-input" type="checkbox" value="1" id="representative-active" name="active"
                            {{ old('active', $representative->active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="representative-active">Active</label>
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <label for="representativename">Name:</label>
                    <span id="name-error" class="error text-danger">@error('name') {{ $message }} @enderror</span>
                </div>
                <div class="form-group col-md-4">
                    <input type="text" id="representativename" name="name" value="{{ old('name', $representative->name) }}"
                           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}">
                </div>
                <div class="d-flex align-items-center">
                    <label for="representativeemail">Email:</label>
                    <span id="email-error" class="error text-danger">@error('email') {{ $message }} @enderror</span>
                </div>
                <div class="form-group col-md-4">
                    <input type="email" id="representativeemail" name="email" value="{{ old('email', $representative->email) }}"
                           class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}">
                </div>
                <div class="d-flex align-items-center">
                    <label for="representativepassword">Password:</label>
                    <span id="password-error" class="error text-danger">@error('password') {{ $message }} @enderror</span>
                </div>
                <div class="form-group col-md-4">
                    <input type="text" id="representativepassword" name="password" value="{{ old('password') }}"
                           class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}">
                </div>
                <div class="d-flex align-items-center">
                    <label for="representativeorganisation">Organisation:</label>
                    <span id="organisation-error" class="error text-danger">@error('organisation') {{ $message }} @enderror</span>
                </div>
                <div class="form-group col-md-6">
                    <select id="representativeorganisation" name="organisation" class="form-control" required class="form-control {{ $errors->has('organisation_name') ? 'is-invalid' : '' }}">>
                        <option value="" >Select organisation...</option>
                        @foreach(App\Models\Organisation::all()->where('active',1) as $organisation)
                            <option value="{{ $organisation->id }}"
                                    @if($organisation->id == ((isset($representative->organisation_id) ? old('organisation', $representative->organisation_id) : '' )))
                                        selected="selected"
                                @endif
                            >{{ $organisation->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">

                    <div class="d-flex align-items-center">
                        <label for="representativeapitoken">API Token:</label>
                        <span id="api-token-error" class="error text-danger">@error('api-token') {{ $message }} @enderror</span>
                    </div>
                    <div class="input-group mb-2">
                        <input id="representativeapitoken" name="api-token" type="text" class="form-control"
                               value="{{ old('api-token') }}"
                               aria-label="Representative API Token" aria-describedby="create-api-token" disabled>
                        <button class="btn btn-outline-secondary" type="button" id="create-api-token" disabled>New API Token</button>
                    </div>
                    <div>
                        <em>New tokens will automatically be added to the clipboard - once submitted you will not be able to retrieve this value</em>
                    </div>

                </div>

                @php
                    // Get the abilities for the representative
                    $token = $representative->tokens()->where('tokenable_id', $representative->id)
                    ->where('name', 'RepresentativeToken')->first();

                    if($token) {
                        //echo $token;
                        $abilities = $token->abilities;
                    }

                @endphp

                <div class="d-flex align-items-center">
                    <label for="representativeapiabilities">Access:</label>
                    <span id="api-abilities-error" class="error text-danger">@error('api-abilities') {{ $message }} @enderror</span>
                </div>
                <div class="form-group col-md-6" >
                    <ul style="list-style-type: none;">
                        <li><input type="hidden" name="api-read" value="0">
                            <input type="checkbox" id="apiread" value="1" name="api-read"
                                   {{ (isset($abilities) ? (old('api-read', in_array('read', $abilities)) ? 'checked' : '') : '') }} disabled> Read Data
                        </li>
                        <li>
                            <input type="hidden" name="api-write" value="0">
                            <input type="checkbox" id="apiwrite" value="1" name="api-write"
                                   {{ (isset($abilities) ? (old('api-write', in_array('write', $abilities)) ? 'checked' : '') : '') }} disabled> Write Data
                        </li>
                    </ul>
                </div>


            </form>
        </div>

    </div>

    <script>
        // Get the checkbox and the button elements
        const activeCheckbox = document.getElementById('representative-active');
        const tokenText = document.getElementById('representativeapitoken');
        const tokenButton = document.getElementById('create-api-token');
        const tokenRead = document.getElementById('apiread');
        const tokenWrite = document.getElementById('apiwrite');
        // Check if we have a token
        const tokenExists = {{ $token ? 'true' : 'false' }};


        let tokenGenerated;

        // Default the token generated to false if no token exists
        if (!tokenExists) {
            tokenGenerated = false;
        }

        // Function to toggle the button's disabled state
        function toggleActiveButton() {
            tokenButton.disabled = !activeCheckbox.checked; // Disable if checkbox is unchecked
            tokenText.disabled = !activeCheckbox.checked;

            // If active is selected
            if (activeCheckbox.checked) {

                // And token exists
                if (tokenExists || tokenGenerated) {
                    tokenRead.disabled = false;
                    tokenWrite.disabled = false;
                } else {
                    tokenRead.disabled = true;
                    tokenWrite.disabled = true;
                }

            } else {
                // Disabled the checkboxes
                tokenRead.disabled = true;
                tokenWrite.disabled = true;
            }
        }

        // Set the generated token flag to true
        function toggleTokenGenerated() {
            tokenGenerated = true;
            toggleActiveButton()
        }

        // Attach event listener to checkbox
        activeCheckbox.addEventListener('change', toggleActiveButton);

        tokenButton.addEventListener('click', toggleTokenGenerated);

        // Initialize the button state on page load
        toggleActiveButton();

    </script>

</x-layout>
