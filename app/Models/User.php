<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'is_verified',
        'organization_name',
        'organization_description',
        'contact_number',
        'address',
        'verification_status',
        'legitimacy_document',
        'area_severity',
        'population_count',
        'accessibility',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function donations() {
        return $this->hasMany(Donation::class);
    }

    public function claimedDonations() {
        return $this->hasMany(Donation::class, 'claimed_by');
    }
}