<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'name',
        'active'
    ];

    /**
     * The associated table
     */
    protected $table = 'organisations';

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
    // Return customers needs
    public function responsible_for() {
        return $this->hasManyThrough(
            Property::class,
            Responsibility::class,
            'organisation',
            'postcode',
            'id',
            'postcode'
        );
    }

    public function representatives() {
        return $this->hasMany(Representative::class);
    }
}
