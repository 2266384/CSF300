<x-layout Title="Customers">
    <div class="col content">

        <!-- Table containing the search results of the reference value -->
        <table id="customer-table" class="table table-striped" style="width:100%">
            <thead>
            <tr>
                <th>ID</th>
                <th>Primary Title</th>
                <th>Primary Forename</th>
                <th>Primary Surname</th>
                <th>Primary DoB</th>
                <th>Secondary Title</th>
                <th>Secondary Forename</th>
                <th>Secondary Surname</th>
                <th>Secondary DoB</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->primary_title }}</td>
                    <td>{{ $customer->primary_forename }}</td>
                    <td>{{ $customer->primary_surname }}</td>
                    <td>{{ $customer->primary_dob ? $customer->primary_dob->format('d-m-Y') : "" }}</td>
                    <td>{{ $customer->secondary_title }}</td>
                    <td>{{ $customer->secondary_forename }}</td>
                    <td>{{ $customer->secondary_surname }}</td>
                    <td>{{ $customer->secondary_dob ? $customer->secondary_dob->format('d-m-Y') : "" }}</td>
                    <td><div class="buttoncontainer">
                            @if( $customer->registrations->where('active', '=', 1)->first() === null)
                                <form action="{{route('registrations.create', $customer)}}" method="GET" >
                                    <button class="btn btn-primary btn-sm">Create</button>
                                </form>
                            @else
                                <form action="{{route('customers.show', $customer)}}" method="GET" >
                                    <button class="btn btn-primary btn-sm">Show</button>
                                </form>
                            @endif
                        </div></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @push('scripts')
            <script>
                $(document).ready( function () {
                    $('#customer-table').DataTable({
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
