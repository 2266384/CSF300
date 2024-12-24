<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCode extends Model
{

    /**
     * The primary key
     */
    protected $primaryKey = 'code';

    /**
     * Turn off auto-incrementing for primary key
     */
    public $incrementing = false;

    /**
     * Define the type for the Primary Key
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'code',
        'description',
        'active'
    ];

    /**
     * The associated table
     */
    protected $table = 'service_codes';

    /**
     * The values that should be cast as specific types
     */
    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * Functions for relationships
     */
    // Returns the customers with this Service Code
    public function customers() {
        return $this->hasManyThrough(Customer::class, Service::class, 'code', 'id', 'id', 'service_id');
    }

}
