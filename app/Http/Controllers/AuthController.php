<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:120', 'unique:users'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        Auth::login(User::create($data), true);
        $request->session()->regenerate();

        return redirect()->intended(route('account'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);

        if (! Auth::attempt($credentials, true)) {
            return back()->withErrors(['email' => 'Email ou mot de passe incorrect.'])->onlyInput('email');
        }
        $request->session()->regenerate();

        return redirect()->intended(Auth::user()->is_admin ? route('admin') : route('account'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        rescue(fn () => Password::sendResetLink($request->only('email')));

        // Same answer whether or not the account exists: no email enumeration.
        return back()->with('status', "Si un compte existe avec cet email, un lien pour changer ton mot de passe vient d'être envoyé.");
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function (User $user, string $password) {
            $user->forceFill(['password' => $password])->setRememberToken(Str::random(60));
            $user->save();
            event(new PasswordReset($user));
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Mot de passe changé. Tu peux te connecter.')
            : back()->withErrors(['email' => "Ce lien n'est plus valide. Demande un nouveau lien."]);
    }
}
