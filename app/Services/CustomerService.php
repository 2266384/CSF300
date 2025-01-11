<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService {

    function customerStatus(Customer $customer) {

        // Set default status to LIVE
        $status = 'Live';

        // Count the registrations
        $registrations = $customer->registrations->count();

        // ACTIVE registrations with a REMOVED DATE
        $removed = $customer->registrations->where('active', '=', 1)
            ->where('removed_date', '=', null)
            ->count();

        // Count the number of properties related to the Customer
        $properties = $customer->properties->count();

        if($registrations == 0) {
            $status = '';
        }
        // Check for removals
        elseif($removed == 0) {
            $status = 'Services no longer needed';
        }
        // Check if we have no properties
        elseif($properties == 0) {
            $status = 'Leaver';
        }

        return $status;
    }
}
