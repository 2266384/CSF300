<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'source',
        'active',
    ];

    /**
     * The associated table
     */
    protected $table = 'sources';

    /**
     * The primary key
     */
    protected $primaryKey = 'id';

    /**
     * The values that should be cast as specific types
     */
    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * Functions for relationships
     */
    // Returns customers for this source
    public function customers()
    {
        return $this->hasManyThrough(
            Customer::class,                // Destination table
            Registration::class,           // Intermediate table
            'id',                          // FK on the Intermediate table
            'id',                       // FK on the Destination table
            'registration_id',            // Local Key on Parent table
            'customer'              // Local Key on the Intermediate table
        );
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'id', 'source' );
    }

}
