<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Tryon;
use App\Models\Wig;
use App\Notifications\SalonNotice;
use App\Services\WigTryOn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/** The wig catalogue with the AI fitting room beside it. */
class ShopController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            session()->put('url.intended', route('shop'));
        }

        return view('shop', [
            'wigs' => Wig::latest()->get(),
            'tryons' => $user ? $user->tryons()->with('wig')->take(12)->get() : collect(),
            'current' => $user ? $user->tryons()->with('wig')->find($request->integer('essai')) ?? $user->tryons()->with('wig')->first() : null,
            'aiReady' => (bool) config('salon.gemini_key'),
        ]);
    }

    public function uploadSelfie(Request $request)
    {
        $request->validate(['selfie' => ['required', 'image', 'max:10240']]);
        $user = $request->user();

        if ($user->selfie_path) {
            Storage::disk('local')->delete($user->selfie_path);
        }
        $user->forceFill(['selfie_path' => $request->file('selfie')->store('selfies', 'local')])->save();

        return redirect()->route('shop')->with('status', 'Photo enregistrée. Choisis une perruque et touche « Essayer ».');
    }

    public function deleteSelfie(Request $request)
    {
        $user = $request->user();
        Storage::disk('local')->delete(array_filter([$user->selfie_path, ...$user->tryons()->pluck('result_path')]));
        $user->tryons()->delete();
        $user->forceFill(['selfie_path' => null])->save();

        return redirect()->route('shop')->with('status', 'Ta photo et tes essais ont été supprimés.');
    }

    public function tryOn(Request $request, Wig $wig, WigTryOn $tryOn)
    {
        $user = $request->user();
        if (! $user->selfie_path) {
            return back()->withErrors(['selfie' => "Ajoute d'abord une photo de toi, de face."]);
        }

        // Each try-on costs real money on the Gemini account.
        $key = 'tryon:'.$user->id;
        if (RateLimiter::tooManyAttempts($key, config('salon.tryons_per_day'))) {
            return back()->withErrors(['selfie' => "Tu as atteint la limite d'essais pour aujourd'hui. Reviens demain."]);
        }
        RateLimiter::hit($key, 86400);

        try {
            $path = $tryOn($user, $wig);
        } catch (RuntimeException $e) {
            return back()->withErrors(['selfie' => $e->getMessage()]);
        }

        $tryon = Tryon::create(['user_id' => $user->id, 'wig_id' => $wig->id, 'result_path' => $path]);

        return redirect()->route('shop', ['essai' => $tryon->id]);
    }

    public function order(Request $request, Wig $wig)
    {
        abort_unless($wig->in_stock, 404);
        $data = $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        $order = Order::create(['user_id' => $request->user()->id, 'wig_id' => $wig->id, 'price' => $wig->price, 'note' => $data['note'] ?? null]);

        SalonNotice::admins('Nouvelle commande',
            $request->user()->name.' a commandé « '.$wig->name.' » ('.number_format($order->price, 0, ',', ' ').' RWF).',
            route('admin'));

        return redirect()->route('account')->with('status', 'Commande envoyée. Tu paies sur place, au retrait.');
    }

    /** Private images (selfies, try-on results): only their owner or an admin may see them. */
    public function selfie(Request $request)
    {
        $path = $request->user()->selfie_path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, ['Cache-Control' => 'private, max-age=3600']);
    }

    public function tryonImage(Request $request, Tryon $tryon)
    {
        abort_unless($tryon->user_id === $request->user()->id || $request->user()->is_admin, 403);

        return Storage::disk('local')->response($tryon->result_path, null, ['Cache-Control' => 'private, max-age=86400']);
    }
}
