<?php

use App\Models\Booking;
use App\Models\User;
use App\Notifications\SalonNotice;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Runs on every deploy (pre-deploy command): creates Niomba's admin account from
// ADMIN_EMAIL / ADMIN_PASSWORD before anyone could register that email. Never
// overwrites the password of an existing account.
Artisan::command('app:admin', function () {
    $email = config('salon.admin_email');
    if (! $email) {
        return $this->info('ADMIN_EMAIL not set, skipping.');
    }
    $user = User::firstOrNew(['email' => $email]);
    if (! $user->exists) {
        $password = env('ADMIN_PASSWORD');
        if (! $password || strlen($password) < 10) {
            return $this->error('ADMIN_PASSWORD missing or shorter than 10 characters.');
        }
        $user->fill(['name' => 'Niomba', 'phone' => config('salon.phone'), 'password' => $password]);
    }
    $user->forceFill(['is_admin' => true])->save();
    $this->info("Admin ready: $email");
})->purpose('Create or promote the salon admin account');

Artisan::command('app:remind', function () {
    $bookings = Booking::with('user', 'service')->where('status', 'confirmed')->where('reminded', false)
        ->whereBetween('starts_at', [now(), now()->addDay()])->get();

    foreach ($bookings as $booking) {
        SalonNotice::send($booking->user, 'Rappel de rendez-vous',
            'Petit rappel : :service, :when. Un empêchement ? Préviens-nous sur WhatsApp.',
            route('account'), ['service' => $booking->service->name, 'when' => $booking->starts_at]);
        $booking->update(['reminded' => true]);
    }
    $this->info($bookings->count().' reminder(s) sent.');
})->purpose('Remind clients of confirmed bookings in the next 24h');

Schedule::command('app:remind')->hourly();
