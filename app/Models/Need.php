<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Need extends Model
{


    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'registration_id',
        'code',
        'temp_end_date',
        'active',
        'lastupdate_id',
        'lastupdate_type'
    ];

    /**
     * Turn off the automatic timestamps
     */
    public $timestamps = false;

    /**
     * The associated table
     */
    protected $table = 'needs';

    /**
     * The primary key
     */
    protected $primaryKey = 'id';

    /**
     * Functions for relationships
     */
    // Returns the customers with this code
    public function customers() {
        return $this->hasManyThrough(
            Customer::class,                // Destination table
            Registration::class,           // Intermediate table
            'id',                          // FK on the Intermediate table
            'id',                       // FK on the Destination table
            'registration_id',            // Local Key on Parent table
            'customer'              // Local Key on the Intermediate table
        );
    }

    // Return the details of the Need Code
    public function description() {
        return $this->belongsTo(NeedCode::class, 'code', 'code');
    }

    /**
     * Function for returning the Polymorphic Relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function lastupdate() {
        return $this->morphTo();
    }

}
