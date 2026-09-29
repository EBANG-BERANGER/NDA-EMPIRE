<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** FR/EN: ?lang= switch > session > account > browser language > French. */
class SetLocale
{
    public const LOCALES = ['fr', 'en'];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (in_array($lang = $request->query('lang'), self::LOCALES, true)) {
            $request->session()->put('locale', $lang);
            if ($user && $user->locale !== $lang) {
                $user->forceFill(['locale' => $lang])->save();
            }
        }

        $locale = $request->session()->get('locale')
            ?? $user?->locale
            ?? $request->getPreferredLanguage(self::LOCALES)
            ?? 'fr';

        app()->setLocale($locale);

        return $next($request);
    }
}
