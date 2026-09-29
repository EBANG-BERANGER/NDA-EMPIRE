<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tryon extends Model
{
    protected $fillable = ['user_id', 'wig_id', 'result_path'];

    public function wig(): BelongsTo
    {
        return $this->belongsTo(Wig::class);
    }
}
