<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Representative extends Authenticatable
{

    // Allows for creation of API Keys
    use HasApiTokens, HasFactory, Notifiable;


    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'organisation_id',
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
        return $this->belongsTo(Organisation::class, 'organisation_id', 'id');
    }


    /**
     * Polymorphic Relation for Representative to be recorded in both Need and Service Class
     *
     * @return MorphMany
     */
    public function updatedneeds() {
        return $this->morphMany(Need::class, 'lastupdate', 'lastupdate_type', 'lastupdate_id', 'id');
    }

    public function updatedservices() {
        return $this->morphMany(Service::class, 'lastupdate', 'lastupdate_type', 'lastupdate_id', 'id');
    }


}
