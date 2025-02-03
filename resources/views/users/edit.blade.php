<x-layout Title="Users">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>{{ $user->name }}</H2>
            </div>
            <button type="button" id="update-user" class="btn p-0 border-0 bg-transparent" title="Save Update"><i class="bi bi-floppy fs-2"></i></button>
            <a href="{{route('users.show', $user)}}" id="cancel-save" title="Cancel Update"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>


        <div>
            <form id="user-data" method="POST" action="{{route('users.update', $user)}}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-sm-1">
                        <label for="userid">User ID:</label>
                        <!-- Check if this is an existing registrant and display their id -->
                        <input type="text" readonly class="form-control-plaintext"  id="userid" name="user-id" value="{{ $user->id }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="username">Name:</label>
                        <input type="text" class="form-control" id="username" name="user-name" value="{{ old('user-name', $user->name) }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="useremail">Email:</label>
                        <input type="email" class="form-control" id="useremail" name="user-email" value="{{ old('user-email', $user->email) }}">
                    </div>
                    <div class="form-group col-sm-1 checkbox">
                        <input class="form-check-input" type="checkbox" value="1" id="userisadmin" name="user-isadmin"
                            {{ old('user-isadmin', $user->is_admin) ? 'checked' : '' }}>
                        <label class="form-check-label" for="userisadmin">Admin</label>
                    </div>
                </div>
            </form>
        </div>



    </div>
</x-layout>
