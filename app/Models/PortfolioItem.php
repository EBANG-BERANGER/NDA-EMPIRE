<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PortfolioItem extends Model
{
    protected $fillable = ['image_path', 'caption'];

    public function imageUrl(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }
}
