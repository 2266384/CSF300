@php

    // Get the current route being used for displaying the form so we can adjust its behaviour
    use Illuminate\Support\Facades\Route;
    $route = Route::currentRouteName();

     // Get the registration details
    $registration = $customer->registrations->where('active', '=', 1)->first();

    // set the variables for use in the form control
    $readonly = 'readonly class=form-control-plaintext';
    $editable = 'class=form-control';
    $disabled = 'disabled';
    $enabled = '';

    // Define the actions based on the route
    switch(true) {
        case in_array($route, ["customers.show"]):
            $psrid = $readonly;
            $sapbpref = $readonly;
            $custref = $readonly;
            $status = $readonly;
            $source = $disabled;
            $consent = $readonly;
            $removed = $readonly;
            $acctname = $readonly;
            $recipient = $readonly;
            if(!isset($registration->removed_date)) {
                $removedVisible = 'hidden';
            } else {
                $removedVisible = null;
            }
            if(!isset($registration->consent_date)) {
                $consentVisible = 'hidden';
            } else {
                $consentVisible = null;
            }
            break;
        case in_array($route, ["registrations.create"]):
            $psrid = $readonly;
            $sapbpref = $readonly;
            $custref = $readonly;
            $status = $readonly;
            $source = $enabled;
            $consent = $editable;
            $removed = $readonly;
            $acctname = $readonly;
            $recipient = $editable;
            $removedVisible = null;
            $consentVisible = null;
            break;
        case in_array($route, ["customers.edit"]):
            $psrid = $readonly;
            $sapbpref = $readonly;
            $custref = $readonly;
            $status = $readonly;
            $source = $disabled;
            $consent = $editable;
            $removed = $editable;
            $acctname = $readonly;
            $recipient = $editable;
            $removedVisible = null;
            $consentVisible = null;
            break;
        default:
            $psrid = $editable;
            $sapbpref = $editable;
            $custref = $editable;
            $status = $editable;
            $source = $enabled;
            $consent = $editable;
            $removed = $editable;
            $acctname = $editable;
            $recipient = $editable;
            $removedVisible = null;
            $consentVisible = null;
    }

    //dd($route);
    //dd($registration->source);
    //getAllAttributes();

@endphp


<form id="registrant-data">

    <div class="row">
        <div class="form-group col-md-2">
            <label for="psrid">PSR ID:</label>
            <!-- Check if this is an existing registrant and display their id -->
            <input type="text" {{ $psrid }} id="psrid" name="psr_id" value=
                @if( in_array($action, array('display', 'edit')))
                "{{ $registration->id }}"
                @endif
            >
        </div>
        <div class="form-group col-md-3">
            <label for="sapbpref">SAP Business Partner:</label>
            <input type="text" {{ $sapbpref }} id="sapbpref" name="sap_bp_ref" value="{{ $customer->sap_reference }}">
        </div>
        <div class="form-group col-md-3">
            <label for="custref">Customer Reference:</label>
            <input type="text" {{ $custref }} id="custref" name="customer_ref" value="{{ $customer->id }}">
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-3">
            <label for="status">Status:</label>
            <input type="text" {{ $status }} id="status" name="status"
                   value="{{ $customerService->customerStatus($customer) }}">
        </div>

        <div class="form-group col-md-4">
            <label for="source">Registration Source:</label>
            <span id="source_name-error" class="error text-danger">@error('source_name') {{ $message }} @enderror</span>
            <select id="source" name="source" class="form-control" {{ $source }} required class="form-control {{ $errors->has('source_name') ? 'is-invalid' : '' }}">>
                @foreach(App\Models\Source::all()->where('active',1) as $source)
                    <option value="{{ $source->id }}"
                            @if($source->id == ((isset($registration->source) ? old('source', $registration->source) : '' )))
                                selected="selected"
                        @endif
                    >{{ $source->source }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-2">
            <label for="consent">Consent to Share:</label>
            <input type="date" {{ $consent }} class="form-control" id="consent" name="consent_date"
                   value="{{ isset($registration->consent_date) ? old('consent_date', $registration->consent_date) : null }}" {{$consentVisible}}>
        </div>

        <div class="form-group col-md-2">
            <label for="remove">Date Removed:</label>
            <input type="date" {{ $removed }} class="form-control" id="remove" name="removed_date"
                   value="{{ isset($registration->removed_date) ? old('remove_date', $registration->removed_date) : null }}" {{ $removedVisible }}>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-4">
            <label for="accountname">Account Name:</label>
            <input type="text" {{ $acctname }} id="accountname" name="account_name" value="{{ $customer->customer_names }}">
        </div>
        <div class="form-group col-md-4">
            <label for="recipientname">Recipient Name:</label>
            <span id="recipient_name-error" class="error text-danger">@error('recipient_name') {{ $message }} @enderror</span>
            <input type="text" {{ $recipient }} id="recipientname" name="recipient_name"
                   value="{{ isset($registration->recipient_name) ? old('recipient_name', $registration->recipient_name) : '' }}"
                   required class="form-control {{ $errors->has('recipient_name') ? 'is-invalid' : '' }}">
        </div>
    </div>

</form>

@if(in_array($route, ['registrations.create', 'customers.edit']))
    <livewire:attribute-list :customer="$customer"/>
@endif
