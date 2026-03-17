<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationClaim extends Model
{
    protected $fillable = ['donation_id', 'charity_id', 'status'];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function charity()
    {
        return $this->belongsTo(User::class, 'charity_id');
    }
}