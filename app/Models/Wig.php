<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Wig extends Model
{
    protected $fillable = ['name', 'description', 'price', 'image_path', 'video_path', 'in_stock'];

    protected $casts = ['in_stock' => 'boolean'];

    public function imageUrl(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }

    public function videoUrl(): ?string
    {
        return $this->video_path ? Storage::disk('public')->url($this->video_path) : null;
    }
}
