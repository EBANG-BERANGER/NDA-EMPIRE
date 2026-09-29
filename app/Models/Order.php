<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    public const STATUSES = ['pending' => 'En attente', 'ready' => 'Prête à récupérer', 'collected' => 'Récupérée', 'cancelled' => 'Annulée'];

    protected $fillable = ['user_id', 'wig_id', 'price', 'status', 'note'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wig(): BelongsTo
    {
        return $this->belongsTo(Wig::class);
    }

    public function statusLabel(): string
    {
        return __(self::STATUSES[$this->status] ?? $this->status);
    }
}
