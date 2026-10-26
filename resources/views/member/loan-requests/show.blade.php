@extends('layouts.member')

@section('title', 'Detail Pengajuan #{{ $loan->id }} — Dream Pawn Shop')

@section('member_content')
<div class="max-w-4xl mx-auto space-y-6">
    <a href="{{ route('member.loan-requests.index') }}" class="text-xs text-slate-400 hover:text-amber-400">&larr; Kembali ke Riwayat Pinjaman</a>
    <section class="glass-card rounded-2xl border border-slate-800 p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <p class="text-xs font-mono text-slate-500">Pengajuan #{{ $loan->id }}</p>
                <h1 class="font-serif-dream text-2xl font-bold text-white mt-1">Detail Peminjaman Props</h1>
                <p class="text-xs text-slate-400 mt-1">Diajukan {{ $loan->requested_at?->format('d M Y, H:i') ?? '-' }}</p>
            </div>
            @include('components.status-badge', ['status' => $loan->status])
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="rounded-xl bg-slate-900/70 border border-slate-800 p-4">
                <p class="text-xs text-slate-500">Disetujui / Ditinjau oleh</p>
                <p class="text-sm text-slate-200 mt-1">{{ $loan->reviewer?->name ?? '-' }}</p>
            </div>
            <div class="rounded-xl bg-slate-900/70 border border-slate-800 p-4">
                <p class="text-xs text-slate-500">Serah-terima fisik</p>
                <p class="text-sm text-slate-200 mt-1">{{ $loan->handed_over_at?->format('d M Y, H:i') ?? '-' }}</p>
            </div>
            <div class="rounded-xl bg-slate-900/70 border border-slate-800 p-4">
                <p class="text-xs text-slate-500">Jatuh tempo pengembalian</p>
                <p class="text-sm text-slate-200 mt-1">{{ $loan->due_at?->format('d M Y, H:i') ?? '-' }}</p>
            </div>
            <div class="rounded-xl bg-slate-900/70 border border-slate-800 p-4">
                <p class="text-xs text-slate-500">Dikonfirmasi kembali oleh</p>
                <p class="text-sm text-slate-200 mt-1">{{ $loan->returnConfirmer?->name ?? '-' }}</p>
            </div>
        </div>
        <div>
            <h2 class="text-xs uppercase tracking-wider text-slate-400 mb-3">Unit Props ({{ $loan->details->count() }})</h2>
            <div class="space-y-3">
                @foreach ($loan->details as $detail)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 rounded-xl bg-slate-900/70 border border-slate-800 p-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-100">{{ $detail->item?->name ?? 'Unit tidak tersedia' }}</p>
                            <p class="text-xs font-mono text-amber-400">{{ $detail->item?->unit_code ?? '-' }}</p>
                        </div>
                        <div class="text-xs text-slate-400">
                            @if ($detail->fine)
                                Denda: <strong class="text-rose-300">Rp{{ number_format($detail->fine->amount, 0, ',', '.') }}</strong>
                                <span>({{ $detail->fine->status }})</span>
                            @else
                                Tidak ada denda tercatat
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @if ($loan->is_overdue)
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm text-rose-200">
                Terlambat {{ $loan->overdue_full_days }} hari penuh. Perkiraan denda saat ini: <strong>Rp{{ number_format($loan->estimated_fine, 0, ',', '.') }}</strong>.
            </div>
        @endif
        @if ($loan->status === 'active')
            <p class="text-sm text-slate-400">Untuk mengembalikan semua unit, hubungi Admin dan serahkan unit di konter agar kondisi serta pengembaliannya dapat dikonfirmasi.</p>
        @endif
        @if ($loan->total_fine > 0)
            <div class="rounded-xl bg-slate-900/70 border border-slate-800 p-4 space-y-1">
                <p class="text-xs text-slate-500">Total denda tercatat</p>
                <p class="text-lg font-bold text-rose-300">Rp{{ number_format($loan->total_fine, 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400">Belum lunas: Rp{{ number_format($loan->unpaid_fine, 0, ',', '.') }}</p>
            </div>
        @endif
        <a href="{{ route('member.loan-requests.index') }}" class="inline-flex px-4 py-2 rounded-xl bg-slate-800 text-slate-200 text-xs font-semibold">Kembali ke Riwayat</a>
    </section>
</div>
@endsection
