<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Representative extends Authenticatable
{


    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'organisation',
        'APIKey',
        'active'
    ];

    /**
     * The associated table
     */
    protected $table = 'representatives';

    /**
     * The primary key
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be hidden
     */
    protected $hidden = [
        'password',
        'APIKey'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Functions for relationships
     */
    // Return customers organisation of representative
    public function represents() {
        return $this->belongsTo(Organisation::class, 'organisation');
    }


    /**
     * Polymorphic Relation for Representative to be recorded in both Need and Service Class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function updatedneeds() {
        return $this->morphMany(Need::class, 'lastupdate', 'lastupdate_type', 'lastupdate_id');
    }

    public function updatedservices() {
        return $this->morphMany(Service::class, 'lastupdate', 'lastupdate_type', 'lastupdate_id');
    }


}
