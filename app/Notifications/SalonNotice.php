<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as Notifier;
use Throwable;

/** One notice type for everything: shows in the in-app bell and goes out by email. */
class SalonNotice extends Notification
{
    public function __construct(public string $title, public string $body, public string $url) {}

    /** Mail failures (e.g. SMTP not configured yet) must never break a booking or an order. */
    public static function send(mixed $users, string $title, string $body, string $url): void
    {
        try {
            Notifier::send($users, new self($title, $body, $url));
        } catch (Throwable $e) {
            Log::warning('Notification not fully delivered: '.$e->getMessage());
        }
    }

    public static function admins(string $title, string $body, string $url): void
    {
        self::send(User::where('is_admin', true)->get(), $title, $body, $url);
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return ['title' => $this->title, 'body' => $this->body, 'url' => $this->url];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title.' — '.config('salon.name'))
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($this->body)
            ->action('Voir', $this->url)
            ->salutation(config('salon.name').' '.config('salon.signature'));
    }
}
