<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Baggage extends Model
{
    protected $table = 'baggages';

    protected $fillable = [
        'trip_id', 'nama_pengirim', 'no_hp_pengirim',
        'nama_penerima', 'no_hp_penerima',
        'jenis_barang', 'keterangan', 'jumlah', 'diinput_oleh',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }
}
