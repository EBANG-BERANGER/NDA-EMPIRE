<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Order;
use App\Models\PortfolioItem;
use App\Models\Service;
use App\Models\User;
use App\Models\Wig;
use App\Notifications\SalonNotice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'pending' => Booking::with('user', 'service')->where('status', 'pending')->where('starts_at', '>=', now()->subDay())->orderBy('starts_at')->get(),
            'agenda' => Booking::with('user', 'service')->where('status', 'confirmed')->whereBetween('starts_at', [now()->startOfDay(), now()->addDays(14)])->orderBy('starts_at')->get()->groupBy(fn ($b) => $b->starts_at->toDateString()),
            'toClose' => Booking::with('user', 'service')->where('status', 'confirmed')->where('starts_at', '<', now()->startOfDay())->orderBy('starts_at')->get(),
            'orders' => Order::with('user', 'wig')->whereIn('status', ['pending', 'ready'])->oldest()->get(),
            'stats' => [
                'clientes' => User::where('is_admin', false)->count(),
                'nouvelles ce mois' => User::where('is_admin', false)->where('created_at', '>=', now()->startOfMonth())->count(),
                'visites ce mois' => Booking::where('status', 'done')->where('starts_at', '>=', now()->startOfMonth())->count(),
            ],
        ]);
    }

    public function bookingStatus(Request $request, Booking $booking)
    {
        $status = $request->validate(['status' => ['required', Rule::in(array_keys(Booking::STATUSES))]])['status'];
        $booking->update(['status' => $status]);

        $when = $booking->starts_at->translatedFormat('l j F à H:i');
        $message = match ($status) {
            'confirmed' => 'Ton rendez-vous est confirmé : '.$booking->service->name.', '.$when.'. À très vite !',
            'cancelled' => 'Ton rendez-vous du '.$when.' a été annulé. Contacte-nous sur WhatsApp pour en trouver un autre.',
            'done' => 'Merci pour ta visite ! Montre-nous ta nouvelle coiffure en nous identifiant.',
            default => null,
        };
        if ($message) {
            SalonNotice::send($booking->user, 'Rendez-vous '.mb_strtolower($booking->statusLabel()), $message, route('account'));
        }

        return back()->with('status', 'Rendez-vous mis à jour.');
    }

    public function orderStatus(Request $request, Order $order)
    {
        $status = $request->validate(['status' => ['required', Rule::in(array_keys(Order::STATUSES))]])['status'];
        $order->update(['status' => $status]);

        $message = match ($status) {
            'ready' => 'Ta perruque « '.$order->wig->name.' » est prête. Passe la récupérer au salon.',
            'cancelled' => 'Ta commande « '.$order->wig->name.' » a été annulée. Contacte-nous sur WhatsApp pour en savoir plus.',
            default => null,
        };
        if ($message) {
            SalonNotice::send($order->user, 'Commande '.mb_strtolower($order->statusLabel()), $message, route('account'));
        }

        return back()->with('status', 'Commande mise à jour.');
    }

    public function clients(Request $request)
    {
        $search = trim((string) $request->query('q'));

        return view('admin.clients', [
            'q' => $search,
            'clients' => User::where('is_admin', false)
                ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%$search%")->orWhere('phone', 'like', "%$search%")->orWhere('email', 'like', "%$search%")))
                ->withCount(['bookings as visits' => fn ($q) => $q->where('status', 'done'), 'orders'])
                ->withMax(['bookings as last_visit' => fn ($q) => $q->where('status', 'done')], 'starts_at')
                ->latest()->paginate(30)->withQueryString(),
        ]);
    }

    public function client(User $user)
    {
        return view('admin.client', [
            'client' => $user,
            'bookings' => $user->bookings()->with('service')->get(),
            'orders' => $user->orders()->with('wig')->get(),
            'tryons' => $user->tryons()->with('wig')->take(12)->get(),
        ]);
    }

    public function services()
    {
        return view('admin.services', ['services' => Service::orderBy('category')->orderBy('name')->get()]);
    }

    public function saveService(Request $request, ?Service $service = null)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:40'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:600'],
            'price' => ['required', 'integer', 'min:0'],
        ]);
        $service ? $service->update($data) : Service::create($data);

        return back()->with('status', 'Prestation enregistrée.');
    }

    public function deleteService(Service $service)
    {
        if (Booking::where('service_id', $service->id)->exists()) {
            return back()->withErrors(['service' => 'Cette prestation a déjà des rendez-vous : change son nom ou son prix plutôt que de la supprimer.']);
        }
        $service->delete();

        return back()->with('status', 'Prestation supprimée.');
    }

    public function wigs()
    {
        return view('admin.wigs', ['wigs' => Wig::latest()->get()]);
    }

    public function storeWig(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'integer', 'min:0'],
            'image' => ['required', 'image', 'max:10240'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:61440'],
        ]);

        Wig::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'image_path' => $request->file('image')->store('wigs', 'public'),
            'video_path' => $request->file('video')?->store('wigs', 'public'),
        ]);

        return back()->with('status', 'Perruque ajoutée au catalogue.');
    }

    public function updateWig(Request $request, Wig $wig)
    {
        $wig->update($request->validate([
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'integer', 'min:0'],
            'in_stock' => ['boolean'],
        ]) + ['in_stock' => false]);

        return back()->with('status', 'Perruque mise à jour.');
    }

    public function deleteWig(Wig $wig)
    {
        if (Order::where('wig_id', $wig->id)->exists()) {
            $wig->update(['in_stock' => false]);

            return back()->with('status', 'Cette perruque a des commandes : elle est marquée « épuisée » au lieu d\'être supprimée.');
        }
        Storage::disk('public')->delete(array_filter([$wig->image_path, $wig->video_path]));
        $wig->delete();

        return back()->with('status', 'Perruque supprimée.');
    }

    public function portfolio()
    {
        return view('admin.portfolio', ['items' => PortfolioItem::latest()->get()]);
    }

    public function storePortfolio(Request $request)
    {
        $request->validate([
            'images' => ['required', 'array', 'max:20'],
            'images.*' => ['image', 'max:10240'],
            'caption' => ['nullable', 'string', 'max:160'],
        ]);
        foreach ($request->file('images') as $image) {
            PortfolioItem::create(['image_path' => $image->store('portfolio', 'public'), 'caption' => $request->input('caption')]);
        }

        return back()->with('status', 'Réalisations ajoutées.');
    }

    public function deletePortfolio(PortfolioItem $item)
    {
        Storage::disk('public')->delete($item->image_path);
        $item->delete();

        return back()->with('status', 'Photo retirée.');
    }
}
