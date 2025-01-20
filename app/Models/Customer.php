<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Relationship;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Customer extends Model
{

    use Searchable;
    /**
     * Searchable elements
     */
    public function toSearchableArray() {
        return [
            'id' => $this->id,
            'sapref' => $this->SAP_reference,
            'primary_forename' => $this->primary_forename,
            'primary_surname' => $this->primary_surname,
            'secondary_forename' => $this->secondary_forename,
            'secondary_surname' => $this->secondary_surname,
        ];
    }

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'SAP_reference',
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
        return $this->hasManyThrough(
            Need::class,
            Registration::class,
            'customer',
            'registration_id',
            'id',
            'id'
        );
        //->where('active', 1);
    }

    // Return customers services
    public function services() {
        return $this->hasManyThrough(
            Service::class,
            Registration::class,
            'customer',
            'registration_id',
            'id',
            'id'
        );
            //->where('active', 1);
    }

    public function registrations() {
        return $this->hasMany(Registration::class, 'customer' );
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


    /**
     * Accessor function to return the full concatenated first and second names
     * from the account
     * @return string
     */
    public function getCustomerNamesAttribute()
    {

        // Get the individual primary and secondary names
        $primaryParts = array_filter([
            'title' => $this->primary_title ?? null,
            'forename' => $this->primary_forename ?? null,
            'surname' => $this->primary_surname ?? null
        ]);
        $secondaryParts = array_filter([
            'title' => $this->secondary_title ?? null,
            'forename' => $this->secondary_forename ?? null,
            'surname' => $this->secondary_surname ?? null
        ]);

        // Implode the names to remove NULL values
        $formattedPrimary = implode(' ', $primaryParts);
        $formattedSecondary = implode(' ', $secondaryParts);

        // If Name 2 exists concatenate with an & otherwise return Name 1
        return $formattedSecondary ? $formattedPrimary . ' & ' . $formattedSecondary : $formattedPrimary;
    }

}
