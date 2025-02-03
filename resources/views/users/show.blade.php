@php
    use Carbon\Carbon;
    use App\Models\Registration;
    use App\Models\Customer;
@endphp

<x-layout Title="Users">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>{{ $user->name }}</H2>
            </div>
            <a href="{{ route('users.edit', $user) }}" id="edit-user"><i class="bi bi-pencil-square fs-2"></i></a>
            <a href="{{route('users.index')}}" id="cancel-show-user" title="Cancel Show"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>

        <div>
            <form id="user-data">

                <div class="row">
                    <div class="form-group col-sm-1">
                        <label for="userid">User ID:</label>
                        <!-- Check if this is an existing registrant and display their id -->
                        <input type="text" readonly class="form-control-plaintext"  id="userid" name="user_id" value="{{ $user->id }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="username">Name:</label>
                        <input type="text" readonly class="form-control-plaintext" id="username" name="user_name" value="{{ $user->name }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="useremail">Email:</label>
                        <input type="email" readonly class="form-control-plaintext" id="useremail" name="user-email" value="{{ $user->email }}">
                    </div>
                    <div class="form-group col-sm-1 checkbox">
                        <input class="form-check-input" type="checkbox" value="" id="userisadmin"
                               @if( $user->is_admin )
                                   checked
                               @endif disabled>
                        <label class="form-check-label" for="userisadmin">Admin</label>
                    </div>
                </div>
            </form>
        </div>

        <!-- User activity table -->
        <div class="p2 flex-grow-1" id="user-activity">
            <H4>Latest Activity:</H4>
        </div>

        <div>

            <table id="activity-table" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Activity</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach(latestActivity($user) as $item)
                    <tr>
                        <td>{{ Carbon::parse($item->valid_from)->format('d-m-Y') }}</td>
                        <td>@if ($item->active == 1)
                            Added {{ $item->description->description }} to
                        @else
                            Removed {{ $item->description->description }} from
                        @endif
                        {{ Registration::find($item->registration_id)->registered->customer_names }}
                        </td>
                        <td>
                            <div class="buttoncontainer d-flex">
                                <form action="{{route('customers.show', Customer::find(Registration::find($item->registration_id)->customer))}}" method="GET" >
                                    <button class="btn btn-sm" title="View User"><i class="bi bi-eye fs-5"></i></button>
                                </form>
                            </div>
                        </td>
                </tr>@endforeach
                </tbody>
            </table>

            @push('scripts')
                <script>
                    $(document).ready( function () {
                        $('#activity-table').DataTable({
                                autoWidth: false
                                ,pageLength: 25
                                ,lengthMenu: [10, 25, 50, 100]
                            }
                        );
                    } );
                </script>
            @endpush

        </div>

    </div>
</x-layout>
