<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'uprn',
        'house_number',
        'house_name',
        'street',
        'town',
        'parish',
        'county',
        'postcode',
        'occupier'
    ];

    /**
     * The associated table
     */
    protected $table = 'properties';

    /**
     * The primary key
     */
    protected $primaryKey = 'id';

    /**
     * Functions for relationships
     */
    // Return customers needs
    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function responsibility_of() {
        return $this->belongsTo(Responsibility::class);
    }
}
