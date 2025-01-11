<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{

    protected $table = 'actions';

    protected $fillable = [
        'sourcecode',
        'sourcecode_type',
        'action',
        'targetcode',
        'targetcode_type',
        'active'
    ];

    protected $primaryKey = 'id';

    protected $casts = [
        'active' => 'boolean'
    ];

    public function need() {
        return $this->belongsTo(Need::class, 'sourcecode', 'code');
    }

    public function service() {
        return $this->belongsTo(Service::class, 'sourcecode', 'code');
    }

    /**
     * Function for returning the Polymorphic Relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function sourcecode() {
        return $this->morphTo('sourcecode', 'sourcecode_type', 'sourcecode', 'code');
    }

}
