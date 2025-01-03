<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'primary_title',
        'primary_forename',
        'primary_surname',
        'primary_dob',
        'secondary_title',
        'secondary_forename',
        'secondary_surname',
        'secondary_dob'
    ];

    /**
     * The associated table
     */
    protected $table = 'customers';

    /**
     * The primary key
     */
    protected $primaryKey = 'id';

    /**
     * The values that should be cast as specific types
     */
    protected $casts = [
        'primary_dob' => 'date',
        'secondary_dob' => 'date'
    ];

    /**
     * Functions for relationships
     */
    // Return customers needs
    public function needs() {
        return $this->hasMany(Need::class,'customer');
    }

    // Return customers services
    public function services() {
        return $this->hasMany(Service::class, 'customer');
    }

    // Return customers properties
    public function properties() {
        return $this->hasMany(Property::class, 'occupier');
    }

    // Return customer emails
    public function emails() {
        return $this->hasMany(Email::class);
    }

    // Return customer telephone numbers
    public function telephones() {
        return $this->hasMany(Telephone::class);
    }

}
