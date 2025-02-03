<x-layout Title="Users">
    <div class="col content">

        <div class="text-end" id="user-create-buttoncontainer">
            <a href="{{ route('users.create') }}" id="create-new-user" class="btn btn-primary"><i class="bi bi-file-plus fs-5"> New</i></a>
        </div>

        <table id="user-table" class="table table-striped" style="width:100%">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Is Admin</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach(App\Models\User::all() as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->is_admin }}</td>
                    <td>
                        <div class="buttoncontainer d-flex">
                            <form action="{{route('users.show', $user)}}" method="GET" >
                                <button class="btn btn-sm" title="View User"><i class="bi bi-eye fs-5"></i></button>
                            </form>
                            <form action="{{route('users.destroy', $user->id)}}" method="POST" onsubmit="return confirm('Are you sure you want to remove this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm"><i class="bi bi-trash fs-5"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @push('scripts')
            <script>
                $(document).ready( function () {
                    $('#user-table').DataTable({
                            autoWidth: false
                            ,pageLength: 25
                            ,lengthMenu: [10, 25, 50, 100]
                        }
                    );
                } );
            </script>
        @endpush

    </div>
</x-layout>
