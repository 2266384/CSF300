<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
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
     * Turn off the timestamps
     */
    public $timestamps = false;

    protected $table = 'services';


    /**
     * The primary key
     */
    protected $primaryKey = 'id';

    /**
     * Functions for relationships
     */
    // Returns the customers with this code
    public function customer() {
        return $this->hasMany(Customer::class);
    }

    // Return the details of the Service Code
    public function description() {
        return $this->belongsTo(ServiceCode::class);
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
