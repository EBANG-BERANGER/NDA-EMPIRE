<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return view('account', [
            'upcoming' => $user->bookings()->with('service')->where('starts_at', '>=', now()->startOfDay())->whereIn('status', ['pending', 'confirmed'])->reorder('starts_at')->get(),
            'history' => $user->bookings()->with('service')->where(fn ($q) => $q->where('starts_at', '<', now()->startOfDay())->orWhereNotIn('status', ['pending', 'confirmed']))->take(20)->get(),
            'orders' => $user->orders()->with('wig')->take(20)->get(),
        ]);
    }

    public function notifications(Request $request)
    {
        $notifications = $request->user()->notifications()->take(50)->get();
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return view('notifications', ['notifications' => $notifications]);
    }
}
