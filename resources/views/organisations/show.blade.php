@php
    use App\Models\Representative;
@endphp

<x-layout Title="Organisations">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>{{ $organisation->name }}</H2>
            </div>
            <a href="{{ route('organisations.edit', $organisation) }}" id="update-organisation"><i class="bi bi-pencil-square fs-2"></i></a>
            <a href="{{route('organisations.index')}}" id="cancel-show-organisation" title="Cancel Show"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>

        <div>
            <form id="organisation-data">

                <div class="row">
                    <div class="form-group col-sm-1">
                        <label for="organisationid">ID:</label>
                        <!-- Check if this is an existing registrant and display their id -->
                        <input type="text" readonly class="form-control-plaintext"  id="organisationid" name="organisation_id" value="{{ $organisation->id }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="organisationname">Name:</label>
                        <input type="text" readonly class="form-control-plaintext" id="organisationname" name="organisation_name" value="{{ $organisation->name }}">
                    </div>
                    <div class="form-group col-sm-1 checkbox">
                        <input class="form-check-input" type="checkbox" value="" id="organisationactive"
                               @if( $organisation->active )
                                   checked
                               @endif disabled>
                        <label class="form-check-label" for="organisationactive">Active</label>
                    </div>
                </div>
            </form>
        </div>

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-representatives-tab" data-bs-toggle="tab" data-bs-target="#nav-representatives"
                        type="button" role="tab" aria-controls="nav-current" aria-selected="true">Representatives
                </button>
                <button class="nav-link" id="nav-responsibilities-tab" data-bs-toggle="tab" data-bs-target="#nav-responsibilities"
                        type="button" role="tab" aria-controls="nav-history" aria-selected="false">Responsibilities
                </button>
            </div>
        </nav>

        <div class="tab-content" id="nav-tabContent">

            <!-- Table containing the currently active needs and services -->
            <div class="tab-pane fade show active" id="nav-representatives" role="tabpanel" aria-labelledby="nav-representatives-tab"
                 tabindex="0">

                <!-- Representatives table -->
                <div>

                    <table id="representatives-table" class="table table-striped" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach( $organisation->representatives as $reps)
                            <tr>
                                <td>{{ $reps->id }}</td>
                                <td>{{ $reps->name }}</td>
                                <td>{{ $reps->email }}</td>
                                <td>
                                    <div class="buttoncontainer d-flex">
                                        <form action="{{ route('representatives.show', Representative::find($reps->id)) }}" method="GET" >
                                            <button class="btn btn-sm" title="View Representative"><i class="bi bi-eye fs-5"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>@endforeach
                        </tbody>
                    </table>

                    @push('scripts')
                        <script>
                            $(document).ready( function () {
                                $('#representatives-table').DataTable({
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

            <!-- Table containing the need and service history -->
            <div class="tab-pane fade" id="nav-responsibilities" role="tabpanel" aria-labelledby="nav-responsibilities-tab" tabindex="0">

                <!-- Responsibilities table -->
                <div>

                    <table id="responsibilities-table" class="table table-striped" style="width:100%">
                        <thead>
                        <tr>
                            <th>UPRN</th>
                            <th>Address</th>
                            <th>Postcode</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach( $organisation->responsible_for as $responsibility)
                            <tr>
                                <td>{{ $responsibility->uprn }}</td>
                                <td>{{ propertyAddress($responsibility) }}</td>
                                <td>{{ $responsibility->postcode }}</td>
                            </tr>@endforeach
                        </tbody>
                    </table>

                    @push('scripts')
                        <script>
                            $(document).ready( function () {
                                $('#responsibilities-table').DataTable({
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
        </div>

    </div>
</x-layout>
