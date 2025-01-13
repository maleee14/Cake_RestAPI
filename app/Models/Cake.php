<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cake extends Model
{
    protected $table = 'cakes';
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
