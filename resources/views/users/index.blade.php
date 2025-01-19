<x-layout Title="Users">
    <div class="col content">

        <table id="user-table" class="table table-striped" style="width:100%">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
            </thead>
            <tbody>
            @foreach(App\Models\User::all() as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
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
