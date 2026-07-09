<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 20px; }
        h1 { font-size: 14px; margin: 0 0 2px; }
        .meta { color: #64748b; font-size: 9px; margin-bottom: 12px; }
        .summary { margin-bottom: 16px; }
        .summary td { padding: 4px 12px 4px 0; font-size: 10px; }
        .summary .label { color: #64748b; }
        .summary .value { font-weight: 600; }
        table.main { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.main th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 5px 6px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; }
        table.main td { border: 1px solid #e2e8f0; padding: 5px 6px; }
        table.main tr:nth-child(even) td { background: #f8fafc; }
        .trip-header { background: #f1f5f9; border: 1px solid #e2e8f0; padding: 6px 8px; margin-top: 14px; margin-bottom: 0; font-size: 10px; }
        .trip-header strong { font-size: 11px; }
        .trip-meta { color: #64748b; font-size: 9px; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <h1>Rekap Okupansi</h1>
    <div class="meta">Periode: {{ $bulanLabel }} | Dicetak: {{ now()->format('d/m/Y H:i') }}</div>

    <table class="summary">
        <tr>
            <td class="label">Total Trip</td>
            <td class="value">{{ $trips->count() }}</td>
            <td class="label">Total Penumpang</td>
            <td class="value">{{ $totalFilled }}</td>
            <td class="label">Rata-rata Okupansi</td>
            <td class="value">{{ $avgPct }}%</td>
        </tr>
    </table>

    {{-- Ringkasan --}}
    <table class="main">
        <thead>
            <tr>
                <th style="width:25px">No</th>
                <th>Tanggal</th>
                <th>Jadwal</th>
                <th>Rute</th>
                <th>Armada</th>
                <th>Status</th>
                <th style="width:45px">Terisi</th>
                <th style="width:45px">Kapasitas</th>
                <th style="width:35px">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trips as $i => $item)
                @php $trip = $item['trip']; @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $trip->tanggal_berangkat->format('d/m/Y') }}</td>
                    <td>{{ $trip->schedule->label }}</td>
                    <td>{{ $trip->schedule->route->name }}</td>
                    <td>{{ $trip->bus->nomor_lambung }}</td>
                    <td>{{ $trip->status }}</td>
                    <td>{{ $item['filled'] }}</td>
                    <td>{{ $item['capacity'] }}</td>
                    <td>{{ $item['pct'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Detail per trip --}}
    @foreach($trips as $i => $item)
        @php $trip = $item['trip']; @endphp
        <div class="trip-header">
            <strong>{{ $trip->schedule->route->name }} — {{ $trip->schedule->label }}</strong>
            <span class="trip-meta">
                | {{ $trip->tanggal_berangkat->format('d/m/Y') }}
                | {{ $trip->bus->nomor_lambung }}
                | {{ $item['filled'] }}/{{ $item['capacity'] }} ({{ $item['pct'] }}%)
            </span>
        </div>
        @if($trip->passengers->isEmpty())
            <table class="main"><tr><td style="text-align:center; color:#94a3b8;">Tidak ada penumpang</td></tr></table>
        @else
            <table class="main">
                <thead>
                    <tr>
                        <th style="width:25px">No</th>
                        <th style="width:40px">Kursi</th>
                        <th>Nama Penumpang</th>
                        <th style="width:80px">No. HP</th>
                        <th>Naik</th>
                        <th>Turun</th>
                        <th>Diinput Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trip->passengers->sortBy(fn($p) => $p->seat->nomor_kursi) as $j => $p)
                        <tr>
                            <td>{{ $j + 1 }}</td>
                            <td style="font-weight:600">{{ $p->seat->nomor_kursi }}</td>
                            <td>{{ $p->nama_penumpang }}</td>
                            <td>{{ $p->no_hp ?: '—' }}</td>
                            <td>{{ $p->alamat_naik ?: '—' }}</td>
                            <td>{{ $p->alamat_turun ?: '—' }}</td>
                            <td style="font-weight:500">{{ $p->inputBy?->name ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    <div class="footer">
        Agent Bus — Rekap Okupansi {{ $bulanLabel }} | Total: {{ $totalFilled }} penumpang dari {{ $trips->count() }} trip
    </div>
</body>
</html>
