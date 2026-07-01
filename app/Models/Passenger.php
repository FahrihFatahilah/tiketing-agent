<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Passenger extends Model
{
    protected $fillable = [
        'trip_id', 'seat_id', 'nama_penumpang', 'no_hp',
        'alamat_naik', 'alamat_turun', 'catatan', 'diinput_oleh',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }
}
