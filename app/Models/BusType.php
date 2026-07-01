<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusType extends Model
{
    protected $fillable = ['name', 'total_seat', 'layout_config'];

    protected $casts = ['layout_config' => 'array'];

    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }
}
