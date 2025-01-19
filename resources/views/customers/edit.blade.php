<x-layout Title="Edit Customers">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>{{$customer->primary_title . ' ' . $customer->primary_forename . ' ' . $customer->primary_surname }}</H2>
            </div>
            <button type="submit" id="save-customer" class="btn col-md-1 m-1 btn-success">Save</button>
            <a href="{{route('customers.show', $customer)}}" id="cancel-save-customer" class="btn col-md-1 m-1 btn-warning">Cancel</a>
        </div>


        <!-- Include the form to display the registrant details -->
        @include('forms.registrant', ['action' => 'edit'])

    </div>
</x-layout>
