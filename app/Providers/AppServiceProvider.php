<?php

namespace App\Providers;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('admin', fn (User $user) => $user->is_admin);

        Carbon::setLocale('fr');
        CarbonImmutable::setLocale('fr');
        Paginator::defaultSimpleView('pagination');
        Paginator::defaultView('pagination');

        ResetPassword::toMailUsing(fn ($user, string $token) => (new MailMessage)
            ->subject(__('Changer ton mot de passe — NDA EMPIRE'))
            ->line(__('Tu as demandé à changer ton mot de passe.'))
            ->action(__('Choisir un nouveau mot de passe'), route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->line(__("Ce lien expire dans 60 minutes. Si tu n'as rien demandé, ignore cet email.")));

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }
}
