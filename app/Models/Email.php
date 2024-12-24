<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'customer',
        'address',
        'default'
    ];

    /**
     * The associated table
     */
    protected $table = 'emails';

    /**
     * The primary key
     */
    protected $primaryKey = 'id';

    /**
     * The values that should be cast as specific types
     */
    protected $casts = [
        'default' => 'boolean'
    ];

    /**
     * Functions for relationships
     */
    // Returns the customer
    public function customer() {
        return $this->belongsTo(Customer::class);
    }

}
