<x-layout Title="Need Codes">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>New Need Code:</H2>
            </div>
            <button type="button" id="create-need" class="btn p-0 border-0 bg-transparent" title="Save Need"><i class="bi bi-floppy fs-2"></i></button>
            <a href="{{route('needcodes.index')}}" id="cancel-save-need" title="Cancel Update"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>


        <div>
            <form id="create-need-code-data" enctype="multipart/form-data">
                @csrf
                <div class="d-flex align-items-center">
                    <label for="needcode">Code:</label>
                    <span id="code-error" class="error text-danger">@error('code') {{ $message }} @enderror</span>
                </div>
                <div class="form-group col-sm-1 d-flex align-items-center">
                    <input type="text" id="needcode" name="code" value="{{ old('needcode') }}"
                           class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}">
                </div>
                <div class="d-flex align-items-center">
                    <label for="needdescription">Name:</label>
                    <span id="description-error" class="error text-danger">@error('description') {{ $message }} @enderror</span>
                </div>
                <div class="form-group col-md-4 d-flex align-items-center">
                    <input type="text" id="needdescription" name="description" value="{{ old('needdescription') }}"
                           class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}">
                </div>
                <div class="form-group col-sm-1 checkbox">
                    <input class="form-check-input" type="checkbox" value="1" id="need-active" name="active"
                        {{ old('active') ? 'checked' : '' }}>
                    <label class="form-check-label" for="need-active">Active</label>
                </div>
            </form>
        </div>


    </div>
</x-layout>
