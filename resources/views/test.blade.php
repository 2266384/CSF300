@php
    $customer = App\Models\Customer::findorfail(735);


    @endphp
<x-layout Title="Test">
    {{ $customer->customer_names }}


    <livewire:attribute-list :parameter="$customer->id"/>

</x-layout>
