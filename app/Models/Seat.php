<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seat extends Model
{
    protected $fillable = ['bus_type_id', 'nomor_kursi', 'kategori', 'posisi_row', 'posisi_col'];

    public function busType(): BelongsTo
    {
        return $this->belongsTo(BusType::class);
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(Passenger::class);
    }
}
