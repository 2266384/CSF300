<?php

namespace App\Models;

use App\Services\CustomerService;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;


class Property extends Model
{
    use Searchable;
    /**
     * Searchable elements
     */
    public function toSearchableArray() {

        // Create compiled Full Address to use as a searchable element
        $fulladdress = implode(', ',
            array_filter([
                $this->house_number,
                $this->house_name,
                $this->street,
                $this->town,
                $this->parish,
                $this->county,
                $this->postcode,
            ])
        );

        return [
            'uprn' => $this->uprn,
            'house_number' => $this->house_number,
            'house_name' => $this->house_name,
            'street' => $this->street,
            'town' => $this->town,
            'county' => $this->county,
            'postcode' => $this->postcode,
            'fulladdress' => $fulladdress,
        ];
    }

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

    protected $casts = [
        'vector' => 'array',
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
     * Append the full address calculated field so it can be used
     */
    protected $appends = ['fulladdress'];

    /**
     * Functions for relationships
     */
    // Return customers needs
    public function customer() {
        return $this->belongsTo(Customer::class, 'occupier', 'id');
    }


    public function responsibility_of() {
        return $this->belongsTo(Responsibility::class);
    }

    public function getFulladdressAttribute(): string
    {
        return implode(', ',
            array_filter([
                $this->house_number,
                $this->house_name,
                $this->street,
                $this->town,
                $this->parish,
                $this->county,
                $this->postcode,
            ])
        );
    }
}
