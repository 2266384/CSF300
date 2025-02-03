<x-layout Title="Service Codes">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>Edit Need Code:</H2>
            </div>
            <button type="button" id="save-service" class="btn p-0 border-0 bg-transparent" title="Save Update"><i class="bi bi-floppy fs-2"></i></button>
            <a href="{{route('servicecodes.index')}}" id="cancel-save-service" title="Cancel Update"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>


        <div>
            <form id="service-code-data" method="POST" action="{{route('servicecodes.update', $servicecode)}}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="d-flex align-items-center">
                        <label for="servicecode">Code:</label>
                        <span id="code-error" class="error text-danger">@error('code') {{ $message }} @enderror</span>
                    </div>
                    <div class="form-group col-sm-1">
                        <input type="text" class="form-control-plaintext"  id="servicecode" name="code" value="{{ old('code', $servicecode->code) }}">
                    </div>
                    <div class="d-flex align-items-center">
                        <label for="servicedescription">Name:</label>
                        <span id="description-error" class="error text-danger">@error('description') {{ $message }} @enderror</span>
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="servicedescription" name="description" value="{{ old('description', $servicecode->description) }}">
                    </div>
                    <div class="form-group col-sm-1 checkbox">
                        <input type="hidden" name="active" value="0">
                        <input class="form-check-input" type="checkbox" value="1" id="service-active" name="active"
                            {{ old('active', $servicecode->active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="service-active">Active</label>
                    </div>
                </div>
            </form>
        </div>

    </div>
</x-layout>
