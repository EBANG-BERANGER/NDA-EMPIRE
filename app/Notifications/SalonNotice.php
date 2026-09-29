<?php

namespace App\Notifications;

use App\Models\User;
use DateTimeInterface;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as Notifier;
use Throwable;

/**
 * One notice type for everything: shows in the in-app bell and goes out by email.
 * Title/body are French source strings translated in each recipient's own language
 * (User::preferredLocale) at send time; dates in $params are formatted the same way.
 */
class SalonNotice extends Notification
{
    public function __construct(public string $title, public string $body, public string $url, public array $params = []) {}

    /** Mail failures (e.g. SMTP not configured yet) must never break a booking or an order. */
    public static function send(mixed $users, string $title, string $body, string $url, array $params = []): void
    {
        try {
            Notifier::send($users, new self($title, $body, $url, $params));
        } catch (Throwable $e) {
            Log::warning('Notification not fully delivered: '.$e->getMessage());
        }
    }

    public static function admins(string $title, string $body, string $url, array $params = []): void
    {
        self::send(User::where('is_admin', true)->get(), $title, $body, $url, $params);
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    private function text(string $key): string
    {
        $params = array_map(fn ($v) => $v instanceof DateTimeInterface
            ? Carbon::instance($v)->locale(app()->getLocale())->translatedFormat(__('l j F à H:i'))
            : $v, $this->params);

        return __($key, $params);
    }

    public function toArray(object $notifiable): array
    {
        return ['title' => $this->text($this->title), 'body' => $this->text($this->body), 'url' => $this->url];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->text($this->title).' — NDA EMPIRE')
            ->greeting(__('Bonjour :name,', ['name' => $notifiable->name]))
            ->line($this->text($this->body))
            ->action(__('Voir'), $this->url)
            ->salutation('NDA EMPIRE by Niomba');
    }
}
