<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlashSale extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class);
    }
}
