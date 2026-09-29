<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use App\Models\Service;
use App\Models\Wig;

class SiteController extends Controller
{
    public function home()
    {
        return view('home', [
            'services' => Service::orderBy('category')->orderBy('price')->get()->groupBy('category'),
            'wigs' => Wig::where('in_stock', true)->latest()->take(6)->get(),
            'portfolio' => PortfolioItem::latest()->take(8)->get(),
        ]);
    }

    public function portfolio()
    {
        return view('portfolio', ['items' => PortfolioItem::latest()->paginate(24)]);
    }
}
