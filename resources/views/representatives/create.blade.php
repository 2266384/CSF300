<x-layout Title="Representatives">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>New Representative:</H2>
            </div>
            <button type="button" id="create-representative" class="btn p-0 border-0 bg-transparent" title="Save Representative"><i class="bi bi-floppy fs-2"></i></button>
            <a href="{{route('representatives.index')}}" id="cancel-save-representative" title="Cancel Update"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>


        <div>
            <form id="create-representative-data" enctype="multipart/form-data">
                @csrf
                <div class="d-flex align-items-center">
                    <label for="representativename">Name:</label>
                    <span id="name-error" class="error text-danger">@error('name') {{ $message }} @enderror</span>
                </div>
                <div class="form-group col-md-4">
                    <input type="text" id="representativename" name="name" value="{{ old('representativename') }}"
                           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}">
                </div>
                <div class="d-flex align-items-center">
                    <label for="representativeemail">Email:</label>
                    <span id="email-error" class="error text-danger">@error('email') {{ $message }} @enderror</span>
                </div>
                <div class="form-group col-md-4">
                    <input type="email" id="representativeemail" name="email" value="{{ old('representativeemail') }}"
                           class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}">
                </div>
                <div class="d-flex align-items-center">
                    <label for="representativepassword">Password:</label>
                    <span id="password-error" class="error text-danger">@error('password') {{ $message }} @enderror</span>
                </div>
                <div class="form-group col-md-4">
                    <input type="text" id="representativepassword" name="password" value="{{ old('representativepassword') }}"
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
                                    @if($organisation->id == ((isset($representative->organisation_id) ? old('source', $representative->organisation_id) : '' )))
                                        selected="selected"
                                @endif
                            >{{ $organisation->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-sm-2 checkbox">
                    <input class="form-check-input" type="checkbox" value="1" id="representative-apitoken" name="token"
                        {{ old('token') ? 'checked' : '' }}>
                    <label class="form-check-label" for="representative-apitoken">Generate API token?</label>
                </div>
                <div class="col-md-6">
                    <em>
                        Ticking this box will generate an API token for the representative which will be displayed in a separate window following creation.<br>
                        Once generated, the API token will not be displayed again. Immediately communicate this key to the representative using a secure method.
                    </em>
                </div>


                <div class="form-group col-sm-1 checkbox">
                    <input class="form-check-input" type="checkbox" value="1" id="representative-active" name="active"
                        {{ old('active') ? 'checked' : '' }}>
                    <label class="form-check-label" for="representative-active">Active</label>
                </div>

            </form>
        </div>

    </div>
</x-layout>
