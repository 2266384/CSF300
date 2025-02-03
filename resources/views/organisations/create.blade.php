<x-layout Title="Organisations">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>New Organisation:</H2>
            </div>
            <button type="button" id="create-organisation" class="btn p-0 border-0 bg-transparent" title="Save Organisation"><i class="bi bi-floppy fs-2"></i></button>
            <a href="{{route('organisations.index')}}" id="cancel-save-organisation" title="Cancel Update"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>


        <div>
            <form id="create-organisation-data" enctype="multipart/form-data">
                @csrf
                    <div class="d-flex align-items-center">
                        <label for="organisationname">Name:</label>
                        <span id="name-error" class="error text-danger">@error('name') {{ $message }} @enderror</span>
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" id="organisationname" name="name" value="{{ old('organisationname') }}"
                               class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}">
                    </div>
                    <div class="form-group col-sm-1 checkbox">
                        <input class="form-check-input" type="checkbox" value="1" id="organisation-active" name="active"
                            {{ old('active') ? 'checked' : '' }}>
                        <label class="form-check-label" for="organisation-active">Active</label>
                    </div>

            </form>
        </div>

    </div>
</x-layout>
