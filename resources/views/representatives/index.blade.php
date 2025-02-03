<x-layout Title="Representatives">
    <div class="col content">

        <div class="text-end" id="representative-create-buttoncontainer">
            <a href="{{ route('representatives.create') }}" id="create-new-representative" class="btn btn-primary"><i class="bi bi-file-plus fs-5"> New</i></a>
        </div>

        <table id="representatives-table" class="table table-striped" style="width:100%">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Organisation</th>
                <th>Active</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach(App\Models\Representative::all() as $representative)
                <tr>
                    <td>{{ $representative->id }}</td>
                    <td>{{ $representative->name }}</td>
                    <td>{{ $representative->email }}</td>
                    <td>{{ $representative->represents->name }}</td>
                    <td>{{ $representative->active }}</td>
                    <td>
                        <div class="buttoncontainer d-flex">
                            <form action="{{route('representatives.show', $representative)}}" method="GET" >
                                <button class="btn btn-sm" title="View Representative"><i class="bi bi-eye fs-5"></i></button>
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
