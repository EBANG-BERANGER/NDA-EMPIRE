<?php

namespace App\Models;

use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements HasLocalePreference
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /** Emails and notifications go out in the client's own language. */
    public function preferredLocale(): string
    {
        return $this->locale ?? 'fr';
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class)->latest('starts_at');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function tryons(): HasMany
    {
        return $this->hasMany(Tryon::class)->latest();
    }
}
