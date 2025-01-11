<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NeedCode extends Model
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
    protected $table = 'need_codes';

    /**
     * The values that should be cast as specific types
     */
    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * Functions for relationships
     */
    // Returns the customers with this Need Code
    public function customers() {
        return $this->hasManyThrough(
            Registration::class,
            Need::class,
            'code',
            'id',
            'code',
            'registration_id'
        );
    }


    public function actions() {
        return $this->morphMany(Action::class, 'sourcecode', 'sourcecode_type', 'sourcecode', 'code');
    }

}
