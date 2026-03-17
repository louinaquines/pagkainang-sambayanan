<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'description', 'target_audience', 'status', 'feedback_photo',
        'user_id', 'charity_id', 'claimed_by', 'claimed_at',
        'area_severity', 'expires_at', 'affected_area',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function donor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function charity()
    {
        return $this->belongsTo(User::class, 'charity_id');
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    public function claims()
    {
        return $this->hasMany(DonationClaim::class);
    }
}