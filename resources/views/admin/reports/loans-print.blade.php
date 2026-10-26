<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Pinjaman — Dream Pawn Shop</title>
    <style>
        body { font-family: Arial, sans-serif; color: #172033; margin: 24px; font-size: 12px; }
        h1 { margin: 0 0 6px; font-size: 22px; }
        .meta { color: #4b5563; margin-bottom: 18px; line-height: 1.6; }
        .actions { display: flex; gap: 8px; margin-bottom: 18px; }
        button, .back { border: 0; border-radius: 5px; padding: 10px 14px; background: #111827; color: white; text-decoration: none; cursor: pointer; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #cbd5e1; padding: 7px; text-align: left; vertical-align: top; }
        th { background: #e2e8f0; }
        .muted { color: #64748b; }
        @page { size: landscape; margin: 12mm; }
        @media print {
            body { margin: 0; font-size: 10px; }
            .actions { display: none; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" onclick="window.print()">Cetak / Simpan sebagai PDF</button>
        <a class="back" href="{{ route('admin.reports.loans.index', $filters) }}">Kembali ke Laporan</a>
    </div>

    <h1>Dream Pawn Shop — Laporan Pinjaman</h1>
    <div class="meta">
        Dicetak: {{ now()->format('d-m-Y H:i') }}<br>
        Jumlah transaksi: {{ $report->count() }}<br>
        Filter:
        Status {{ $filters['status'] ?? 'Semua' }},
        Dari {{ $filters['from'] ?? '—' }},
        Sampai {{ $filters['to'] ?? '—' }},
        Member ID {{ $filters['member_id'] ?? 'Semua' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Request</th>
                <th>Member</th>
                <th>Email</th>
                <th>Kode Unit</th>
                <th>Nama Barang</th>
                <th>Status</th>
                <th>Diajukan</th>
                <th>Serah Terima</th>
                <th>Jatuh Tempo</th>
                <th>Dikembalikan</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report as $loan)
                @forelse ($loan->details as $detail)
                    <tr>
                        <td>{{ $loan->id }}</td>
                        <td>{{ $loan->member?->name ?? '—' }}</td>
                        <td>{{ $loan->member?->email ?? '—' }}</td>
                        <td>{{ $detail->item?->unit_code ?? '—' }}</td>
                        <td>{{ $detail->item?->name ?? '—' }}</td>
                        <td>{{ ucfirst($loan->status) }}</td>
                        <td>{{ $loan->requested_at?->format('d-m-Y H:i') ?? '—' }}</td>
                        <td>{{ $loan->handed_over_at?->format('d-m-Y H:i') ?? '—' }}</td>
                        <td>{{ $loan->due_at?->format('d-m-Y H:i') ?? '—' }}</td>
                        <td>{{ $loan->returned_at?->format('d-m-Y H:i') ?? '—' }}</td>
                        <td>Rp {{ number_format($detail->fine?->amount ?? 0, 0, ',', '.') }} ({{ $detail->fine?->status ?? '—' }})</td>
                    </tr>
                @empty
                    <tr>
                        <td>{{ $loan->id }}</td><td>{{ $loan->member?->name ?? '—' }}</td><td>{{ $loan->member?->email ?? '—' }}</td>
                        <td colspan="2" class="muted">Tidak ada detail barang</td><td>{{ ucfirst($loan->status) }}</td>
                        <td>{{ $loan->requested_at?->format('d-m-Y H:i') ?? '—' }}</td><td>—</td><td>—</td><td>—</td><td>Rp 0</td>
                    </tr>
                @endforelse
            @empty
                <tr><td colspan="11" class="muted">Tidak ada transaksi untuk filter yang dipilih.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
