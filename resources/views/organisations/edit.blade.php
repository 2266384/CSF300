<x-layout Title="Organisations">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>Edit Organisation:</H2>
            </div>
            <button type="button" id="update-organisation" class="btn p-0 border-0 bg-transparent" title="Save Update"><i class="bi bi-floppy fs-2"></i></button>
            <a href="{{route('organisations.index')}}" id="cancel-save-organisation" title="Cancel Update"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>


        <div>
            <form id="organisation-data" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <div class="form-group col-sm-1">
                        <label for="organisationid">ID:</label>
                        <input type="text" class="form-control-plaintext"  id="organisationid" name="id" value="{{ $organisation->id }}">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="organisationname">Name:</label>
                        <span id="name-error" class="error text-danger">@error('name') {{ $message }} @enderror</span>
                        <input type="text" class="form-control" id="organisationname" name="name" value="{{ old('name', $organisation->name) }}">
                    </div>

                    <div class="form-group col-sm-1 checkbox">
                        <input class="form-check-input" type="checkbox" value="1" id="organisation-active" name="active"
                            {{ $organisation->active ? 'checked' : '' }}>
                        <label class="form-check-label" for="organisation-active">Active</label>
                    </div>
                </div>
            </form>
        </div>

        <div class="responsibilities col-md-12" >
            <livewire:postcode-dropdown :organisation="$organisation"/>
        </div>

    </div>
</x-layout>
