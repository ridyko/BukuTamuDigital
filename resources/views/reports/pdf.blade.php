<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kunjungan Tamu</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; }
        .meta { margin-bottom: 15px; }
        .meta table { width: 100%; }
        .meta td { vertical-align: top; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        table.data th { background-color: #f2f2f2; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 9px; color: #777; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-active { background-color: #d1fae5; color: #065f46; }
        .badge-out { background-color: #f3f4f6; color: #374151; }
    </style>
</head>
<body>
    <div class="header">
        @if(isset($gSettings['app_logo']))
            <img src="{{ asset('storage/' . $gSettings['app_logo']) }}" style="height: 60px; margin-bottom: 10px;">
        @endif

        <h2>{{ strtoupper($appName) }}</h2>
        <p>{{ $appOrg }}</p>
        <div style="font-size: 10px; color: #666; margin-top: 5px;">{{ \App\Models\Setting::get('app_address') }}</div>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td width="50%">
                    <strong>Periode:</strong> {{ $date_from ? \Carbon\Carbon::parse($date_from)->format('d/m/Y') : '-' }} s/d {{ $date_to ? \Carbon\Carbon::parse($date_to)->format('d/m/Y') : 'Sekarang' }}<br>
                    <strong>Total Data:</strong> {{ count($visitors) }} record
                </td>
                <td width="50%" align="right">
                    <strong>Dicetak oleh:</strong> {{ $user }}<br>
                    <strong>Waktu Cetak:</strong> {{ $generated_at }}
                </td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Kode</th>
                <th width="15%">Nama Tamu</th>
                <th width="15%">Instansi</th>
                <th width="20%">Keperluan</th>
                <th width="15%">Tujuan</th>
                <th width="10%">Check-In</th>
                <th width="10%">Check-Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visitors as $index => $v)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $v->visit_code }}</td>
                <td>{{ $v->name }}</td>
                <td>{{ $v->institution ?? '-' }}</td>
                <td>{{ $v->purpose }}</td>
                <td>{{ $v->host?->name ?? $v->department ?? '-' }}</td>
                <td>{{ $v->check_in_at?->format('d/m/Y H:i') }}</td>
                <td>{{ $v->check_out_at?->format('d/m/Y H:i') ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ $gSettings['app_footer'] ?? 'Dokumen ini dibuat otomatis oleh '.$appName.' '.$appOrg }}
    </div>
</body>
</html>
