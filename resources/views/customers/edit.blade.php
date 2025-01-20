<x-layout Title="Edit Customers">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>{{ $customer->customer_names }}</H2>
            </div>
            <button type="submit" id="save-customer" class="btn p-0 border-0 bg-transparent" title="Save Update"><i class="bi bi-floppy fs-2"></i></button>
            <a href="{{route('customers.show', $customer)}}" id="cancel-save-customer" title="Cancel Update"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>


        <!-- Include the form to display the registrant details -->
        @include('forms.registrant', ['action' => 'edit'])

    </div>
</x-layout>
