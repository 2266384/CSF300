<x-layout Title="Organisations">
    <div class="col content">

        <div class="text-end" id="organisation-create-buttoncontainer">
            <a href="{{ route('organisations.create') }}" id="create-new-organisation" class="btn btn-primary"><i class="bi bi-file-plus fs-5"> New</i></a>
        </div>

        <table id="organisations-table" class="table table-striped" style="width:100%">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Active</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach(App\Models\Organisation::all() as $organisation)
                <tr>
                    <td>{{ $organisation->id }}</td>
                    <td>{{ $organisation->name }}</td>
                    <td>{{ $organisation->active }}</td>
                    <td>
                        <div class="buttoncontainer d-flex">
                            <form action="{{route('organisations.show', $organisation)}}" method="GET" >
                                <button class="btn btn-sm" title="View Organisation"><i class="bi bi-eye fs-5"></i></button>
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
                    $('#organisations-table').DataTable({
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
