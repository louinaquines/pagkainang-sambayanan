<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';
    
    protected $fillable = [
        'donation_id',
        'charity_id',
        'message',
        'photo_path',
        'charity_comment',
        'food_quality_rating',
        'quantity_rating',
    ];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function charity()
    {
        return $this->belongsTo(User::class, 'charity_id');
    }
}