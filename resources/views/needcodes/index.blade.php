<x-layout Title="Need Codes">
    <div class="col content">

        <div class="text-end" id="need-create-buttoncontainer">
                <a href="{{ route('needcodes.create') }}" id="create-new-need" class="btn btn-primary"><i class="bi bi-file-plus fs-5"> New</i></a>
        </div>

        <table id="need-code-table" class="table table-striped" style="width:100%">
            <thead>
            <tr>
                <th>Code</th>
                <th>Description</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach(App\Models\NeedCode::all() as $needcode)
                <tr>
                    <td>{{ $needcode->code }}</td>
                    <td>{{ $needcode->description }}</td>
                    <td>
                        @if ($needcode->active == 1)
                            Active
                        @else
                            Inactive
                        @endif
                    </td>
                    <td>
                        <div class="buttoncontainer d-flex">
                            <form action="{{ route('needcodes.edit', $needcode) }}" method="GET">
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
