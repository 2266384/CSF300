<x-layout Title="Register Customers">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>{{ $customer->customer_names }}</H2>
            </div>
            <button type="submit" id="save-registrant" class="btn p-0 border-0 bg-transparent" title="Save Registration"><i class="bi bi-floppy fs-2"></i></button>
            <a href="{{ url()->previous() }}" id="cancel-save-registration" title="Cancel Registration"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>


        <!-- Include the form to display the registrant details -->
        @include('forms.registrant', ['action' => 'create'])

    </div>
</x-layout>
