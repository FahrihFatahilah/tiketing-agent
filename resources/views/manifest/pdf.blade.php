<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 20px; }
        h1 { font-size: 14px; margin: 0 0 2px; }
        .meta { color: #64748b; font-size: 10px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; }
        td { border: 1px solid #e2e8f0; padding: 6px 8px; }
        tr:nth-child(even) td { background: #f8fafc; }
        .seat { font-weight: 600; }
        .footer { margin-top: 20px; font-size: 10px; color: #94a3b8; }
    </style>
</head>
<body>
    <h1>Manifest Penumpang</h1>
    <div class="meta">
        {{ $trip->schedule->route->name }} — {{ $trip->schedule->label }} |
        {{ $trip->tanggal_berangkat->format('d F Y') }} |
        Armada: {{ $trip->bus->nomor_lambung }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:40px">No</th>
                <th style="width:50px">Kursi</th>
                <th>Nama Penumpang</th>
                <th style="width:100px">No. HP</th>
                <th>Naik dari</th>
                <th>Turun di</th>
                <th>Catatan</th>
                <th>Diinput Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($passengers as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="seat">{{ $p->seat->nomor_kursi }}</td>
                <td>{{ $p->nama_penumpang }}</td>
                <td>{{ $p->no_hp ?: '—' }}</td>
                <td>{{ $p->alamat_naik ?: '—' }}</td>
                <td>{{ $p->alamat_turun ?: '—' }}</td>
                <td>{{ $p->catatan ?: '' }}</td>
                <td>{{ $p->inputBy?->name ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak: {{ now()->format('d/m/Y H:i') }} | Total: {{ $passengers->count() }} penumpang, {{ $baggages->count() }} bagasi
    </div>

    @if($baggages->count())
    <div style="margin-top: 24px;">
        <h1>Daftar Bagasi</h1>
        <table>
            <thead>
                <tr>
                    <th style="width:30px">No</th>
                    <th>Pengirim</th>
                    <th>HP Pengirim</th>
                    <th>Penerima</th>
                    <th>HP Penerima</th>
                    <th>Jenis Barang</th>
                    <th style="width:30px">Jml</th>
                    <th>Keterangan</th>
                    <th>Diinput Oleh</th>
                </tr>
            </thead>
            <tbody>
                @foreach($baggages as $i => $b)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $b->nama_pengirim }}</td>
                    <td>{{ $b->no_hp_pengirim ?: '—' }}</td>
                    <td>{{ $b->nama_penerima }}</td>
                    <td>{{ $b->no_hp_penerima ?: '—' }}</td>
                    <td>{{ $b->jenis_barang }}</td>
                    <td>{{ $b->jumlah }}</td>
                    <td>{{ $b->keterangan ?: '' }}</td>
                    <td>{{ $b->inputBy?->name ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</body>
</html>
