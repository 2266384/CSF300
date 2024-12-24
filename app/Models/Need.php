<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Need extends Model
{


    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'customer',
        'code',
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
        return $this->hasMany(Customer::class, 'id', 'customer');
    }

    // Return the details of the Need Code
    public function description() {
        return $this->belongsTo(NeedCode::class, 'code');
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
