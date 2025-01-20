@php use Carbon\Carbon; @endphp

<x-layout Title="Customers">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>{{ $customer->customer_names }}</H2>
            </div>
            <a href="{{ route('customers.edit', $customer) }}" id="edit-registrant"><i
                    class="bi bi-pencil-square fs-2"></i></a>
        </div>

        <!-- Include the form to display the registrant details -->
        @include('forms.registrant', ['action' => 'display'])

        <hr>

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-current-tab" data-bs-toggle="tab" data-bs-target="#nav-current"
                        type="button" role="tab" aria-controls="nav-current" aria-selected="true">Current
                </button>
                <button class="nav-link" id="nav-history-tab" data-bs-toggle="tab" data-bs-target="#nav-history"
                        type="button" role="tab" aria-controls="nav-history" aria-selected="false">History
                </button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">

            <!-- Table containing the currently active needs and services -->
            <div class="tab-pane fade show active" id="nav-current" role="tabpanel" aria-labelledby="nav-current-tab"
                 tabindex="0">
                <table id="attributes-table" class="table table-striped" style="width:100%">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($attributeService->currentAttributes($customer) as $item)
                        <tr>
                            <td>{{ $item['code'] }}</td>
                            <td>{{ $item['description'] }}</td>
                            <td>{{ Carbon::parse($item['valid_from'])->format('d-m-Y') }}</td>
                            <td>@if (in_array($item['code'], ['32', '33', '34']))
                                    {{ Carbon::parse($item['temp_end_date'])->format('d-m-Y') }}
                                @else
                                    {{ Carbon::parse($item['valid_to'])->format('d-m-Y') }}
                                @endif</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                @push('scripts')
                    <script>
                        $(document).ready(function () {
                            $('#attributes-table').DataTable({
                                    autoWidth: false
                                    , pageLength: 25
                                    , lengthMenu: [10, 25, 50, 100]
                                }
                            );
                        });
                    </script>
                @endpush
            </div>

            <!-- Table containing the need and service history -->
            <div class="tab-pane fade" id="nav-history" role="tabpanel" aria-labelledby="nav-history-tab" tabindex="0">
                <table id="attributeshistory-table" class="table table-striped" style="width:100%">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($attributeService->previousAttributes($customer) as $item)
                        <tr>
                            <td>{{ $item['code'] }}</td>
                            <td>{{ $item['description'] }}</td>
                            <td>{{ $item['valid_from'] }}</td>
                            <td>{{ $item['valid_to'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                @push('scripts')
                    <script>
                        $(document).ready(function () {
                            $('#attributeshistory-table').DataTable({
                                    autoWidth: false
                                    , pageLength: 25
                                    , lengthMenu: [10, 25, 50, 100]
                                }
                            );
                        });
                    </script>
                @endpush
            </div>
        </div>

    </div>
</x-layout>
