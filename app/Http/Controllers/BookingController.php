<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Notifications\SalonNotice;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $service = Service::find($request->integer('service'));
        if (! $request->user()) {
            session()->put('url.intended', $request->fullUrl()); // back to this slot after login
        }
        $date = rescue(fn () => CarbonImmutable::parse($request->query('date') ?: today())->startOfDay(), today()->toImmutable(), false);

        return view('booking', [
            'services' => Service::orderBy('category')->orderBy('price')->get()->groupBy('category'),
            'service' => $service,
            'date' => $date,
            'slots' => $service ? Booking::availableSlots($service, $date) : [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'starts_at' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);
        $service = Service::findOrFail($data['service_id']);
        $start = CarbonImmutable::parse($data['starts_at']);

        // Lock the day's bookings so two clients can't grab the same slot at once.
        $booking = DB::transaction(function () use ($service, $start, $data, $request) {
            Booking::whereIn('status', Booking::BLOCKING)->whereDate('starts_at', $start->toDateString())->lockForUpdate()->get();
            $free = collect(Booking::availableSlots($service, $start->startOfDay()))->contains(fn ($s) => $s->equalTo($start));

            return $free ? Booking::create([
                'user_id' => $request->user()->id,
                'service_id' => $service->id,
                'starts_at' => $start,
                'ends_at' => $start->addMinutes($service->duration_minutes),
                'note' => $data['note'] ?? null,
            ]) : null;
        });

        if (! $booking) {
            return back()->withErrors(['starts_at' => __("Ce créneau n'est plus libre. Choisis-en un autre.")]);
        }

        SalonNotice::admins('Nouvelle réservation', ':name : :service, :when.', route('admin'),
            ['name' => $request->user()->name, 'service' => $service->name, 'when' => $start]);

        return redirect()->route('account')->with('status', __('Réservation envoyée. Tu seras notifiée dès que Niomba la confirme.'));
    }

    public function cancel(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === $request->user()->id && in_array($booking->status, Booking::BLOCKING), 403);
        $booking->update(['status' => 'cancelled']);

        SalonNotice::admins('Réservation annulée', ':name a annulé : :service, :when.', route('admin'),
            ['name' => $request->user()->name, 'service' => $booking->service->name, 'when' => $booking->starts_at]);

        return back()->with('status', __('Réservation annulée.'));
    }
}
