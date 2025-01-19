<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'customer',
        'recipient_name',
        'source',
        'consent_date',
        'removed_date',
        'active'
    ];

    /**
     * The associated table
     */
    protected $table = 'registrations';

    /**
     * The primary key
     */
    protected $primaryKey = 'id';

    /**
     * The values that should be cast as specific types
     */
    protected $casts = [
        'active' => 'boolean',
        'consent_date' => 'string',
        'removed_date' => 'string',
    ];

    /**
     * Functions for relationships
     */
    // Returns the customer
    public function registered() {
        return $this->belongsTo(Customer::class, 'customer', 'id');
    }

    public function needs() {
        return $this->hasMany(Need::class, 'registration_id', 'id');
    }

    public function services() {
        return $this->hasMany(Service::class, 'registration_id', 'id');
    }

    public function registrationSource() {
        return $this->belongsTo(Source::class, 'source', 'id');
    }

}
