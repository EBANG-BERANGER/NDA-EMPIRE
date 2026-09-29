<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    public const STATUSES = ['pending' => 'En attente', 'confirmed' => 'Confirmé', 'done' => 'Venue', 'cancelled' => 'Annulé', 'no_show' => 'Absente'];

    // Statuses that occupy the chair.
    public const BLOCKING = ['pending', 'confirmed'];

    protected $fillable = ['user_id', 'service_id', 'starts_at', 'ends_at', 'status', 'note', 'reminded'];

    protected $casts = ['starts_at' => 'immutable_datetime', 'ends_at' => 'immutable_datetime', 'reminded' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Free start times for $service on $date. One chair: any pending/confirmed
     * booking blocks its whole time range.
     * ponytail: single chair assumed; add a staff_id column if Niomba hires.
     *
     * @return list<CarbonImmutable>
     */
    public static function availableSlots(Service $service, CarbonImmutable $date): array
    {
        $hours = config('salon.hours')[$date->isoWeekday()] ?? null;
        if (! $hours || $date->isBefore(today()) || $date->isAfter(today()->addDays(config('salon.booking_days_ahead')))) {
            return [];
        }

        $open = $date->setTimeFromTimeString($hours[0]);
        $close = $date->setTimeFromTimeString($hours[1]);
        $taken = self::whereIn('status', self::BLOCKING)
            ->where('starts_at', '<', $close)->where('ends_at', '>', $open)
            ->get(['starts_at', 'ends_at']);

        $slots = [];
        for ($start = $open; $start->addMinutes($service->duration_minutes) <= $close; $start = $start->addMinutes(config('salon.slot_minutes'))) {
            $end = $start->addMinutes($service->duration_minutes);
            $clash = $taken->contains(fn ($b) => $b->starts_at < $end && $b->ends_at > $start);
            if (! $clash && $start->isAfter(now()->addHour())) {
                $slots[] = $start;
            }
        }

        return $slots;
    }
}
