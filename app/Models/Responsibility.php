<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Responsibility extends Model
{
    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'organisation',
        'postcode',
    ];

    /**
     * The associated table
     */
    protected $table = 'responsibilities';

    /**
     * The primary key
     */
    protected $primaryKey = 'id';

    /**
     * Functions for relationships
     */
    // Return customers needs
    public function property() {
        //return $this->hasMany('App\Models\Property', 'responsibility_id', 'id');
        return $this->hasMany(Property::class);
    }

    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }
}
