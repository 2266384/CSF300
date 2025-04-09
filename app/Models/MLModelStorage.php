<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MLModelStorage extends Model
{
    use HasFactory;

    protected $table = 'mlmodel';

    protected $fillable = [
        'name',
        'model_data',
        'accuracy',
        'precision',
        'recall',
        'f1_score',
    ];
}
