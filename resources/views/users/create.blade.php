<x-layout Title="Users">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>New User:</H2>
            </div>
            <button type="button" id="save-user" class="btn p-0 border-0 bg-transparent" title="Save User"><i class="bi bi-floppy fs-2"></i></button>
            <a href="{{route('users.index')}}" id="cancel-save-user" title="Cancel Update"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>


        <div>
            <form id="create-user-data" method="POST" action="{{route('users.store')}}" enctype="multipart/form-data">
                @csrf
                <div>
                    <div class="form-group col-md-4">
                        <label for="username">Name:</label>
                        <input type="text" class="form-control" id="username" name="user-name" value="{{ old('username') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="useremail">Email:</label>
                        <input type="email" class="form-control" id="useremail" name="user-email" value="{{ old('useremail') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="userpassword">Password:</label>
                        <input type="password" class="form-control" id="userpassword" name="user-password">
                        @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-sm-1 checkbox">
                        <input type="hidden" name="user-isadmin" value="0">
                        <input class="form-check-input" type="checkbox" value="1" id="userisadmin" name="user-isadmin"
                            {{ old('userisadmin') ? 'checked' : '' }}>
                        <label class="form-check-label" for="userisadmin">Admin</label>
                    </div>
                </div>
            </form>
        </div>


    </div>
</x-layout>
