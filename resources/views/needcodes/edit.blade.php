<x-layout Title="Need Codes">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>Edit Need Code:</H2>
            </div>
            <button type="button" id="save-need" class="btn p-0 border-0 bg-transparent" title="Save Update"><i class="bi bi-floppy fs-2"></i></button>
            <a href="{{route('needcodes.index')}}" id="cancel-save-need" title="Cancel Update"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>


        <div>
            <form id="need-code-data" method="POST" action="{{route('needcodes.update', $needcode)}}" enctype="multipart/form-data">
                @csrf
                <div class="d-flex align-items-center">
                    <label for="needcode">Code:</label>
                    <span id="code-error" class="error text-danger">@error('code') {{ $message }} @enderror</span>
                </div>
                <div class="form-group col-sm-1">
                    <input type="text" class="form-control-plaintext"  id="needcode" name="code" value="{{ old('code', $needcode->code) }}">
                </div>
                <div class="d-flex align-items-center">
                    <label for="needdescription">Name:</label>
                    <span id="description-error" class="error text-danger">@error('description') {{ $message }} @enderror</span>
                </div>
                <div class="form-group col-md-4">
                    <input type="text" class="form-control" id="needdescription" name="description" value="{{ old('description', $needcode->description) }}">
                </div>
                <div class="form-group col-sm-1 checkbox">
                    <input type="hidden" name="active" value="0">
                    <input class="form-check-input" type="checkbox" value="1" id="need-active" name="active"
                        {{ old('active', $needcode->active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="need-active">Active</label>
                </div>
            </form>
        </div>

    </div>
</x-layout>
