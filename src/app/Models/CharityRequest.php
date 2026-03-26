<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharityRequest extends Model
{
    protected $fillable = [
        'charity_id',
        'food_name',
        'description',
        'quantity',
        'urgency',
        'status',
    ];

    public function charity()
    {
        return $this->belongsTo(User::class, 'charity_id');
    }
}