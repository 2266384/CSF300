<x-layout Title="Service Codes">
    <div class="col content">

        <div class="text-end" id="service-create-buttoncontainer">
            <a href="{{ route('servicecodes.create') }}" id="create-new-service" class="btn btn-primary"><i class="bi bi-file-plus fs-5"> New</i></a>
        </div>

        <table id="service-code-table" class="table table-striped" style="width:100%">
            <thead>
            <tr>
                <th>Code</th>
                <th>Description</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach(App\Models\ServiceCode::all() as $servicecode)
                <tr>
                    <td>{{ $servicecode->code }}</td>
                    <td>{{ $servicecode->description }}</td>
                    <td>
                        @if ($servicecode->active == 1)
                            Active
                        @else
                            Inactive
                        @endif
                    </td>
                    <td>
                        <div class="buttoncontainer d-flex">
                            <form action="{{ route('servicecodes.edit', $servicecode) }}" method="GET">
                                <button type="submit" class="btn btn-sm"><i class="bi bi-pencil-square fs-5"></i></button>
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
                    $('#need-code-table').DataTable({
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
