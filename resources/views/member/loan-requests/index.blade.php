@extends('layouts.member')

@section('title', 'Riwayat Pinjaman Saya — Dream Pawn Shop')

@section('member_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif-dream text-2xl font-bold text-white tracking-wide">Riwayat Pengajuan & Pinjaman Saya</h1>
            <p class="text-xs text-slate-400 mt-1">Pantau status persetujuan, tenggat serah-terima fisik, dan batas pengembalian props.</p>
        </div>
        <a href="{{ route('member.catalog.items.index') }}" 
           class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-lg glow-gold self-start sm:self-auto flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Pinjam Props Baru</span>
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-800">
        @php
            $currentStatus = $filters['status'] ?? '';
            $tabs = [
                '' => 'Semua',
                'pending' => 'Pending',
                'approved' => 'Approved (Siap Ambil)',
                'active' => 'Aktif (Sedang Dipinjam)',
                'returned' => 'Selesai (Returned)',
                'rejected' => 'Ditolak',
                'cancelled' => 'Dibatalkan',
            ];
        @endphp

        @foreach ($tabs as $val => $lbl)
            @php
                $tabParams = request()->except(['page', 'status']);
                if ($val !== '') {
                    $tabParams['status'] = $val;
                }
            @endphp
            <a href="{{ route('member.loan-requests.index', $tabParams) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition {{ $currentStatus === $val ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                {{ $lbl }}
            </a>
        @endforeach
    </div>

    <!-- Loan Cards List -->
    <div class="space-y-4">
        @forelse ($loans as $loan)
            @php
                $isOverdue = $loan->is_overdue ?? false;
                $estFine = $loan->estimated_fine ?? 0;
            @endphp
            <div class="glass-card rounded-2xl p-6 border border-slate-800 hover:border-slate-700 transition space-y-4 {{ $isOverdue ? 'border-rose-500/40' : '' }}">
                <!-- Card Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-800/80">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-base font-bold text-amber-400">#{{ $loan->id }}</span>
                        @include('components.status-badge', ['status' => $loan->status])
                        @if ($isOverdue)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30 animate-pulse">
                                Terlambat {{ $loan->overdue_full_days ?? 0 }} Hari
                            </span>
                        @endif
                    </div>
                    <div class="text-xs text-slate-400">
                        Diajukan: {{ $loan->requested_at ? $loan->requested_at->format('d M Y, H:i') : '-' }}
                    </div>
                </div>

                <!-- Unit Props Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Unit Props Fisik ({{ $loan->details->count() }} unit)</h4>
                        <div class="space-y-2">
                            @foreach ($loan->details as $d)
                                <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-amber-400 font-bold">[{{ $d->item->unit_code ?? '-' }}]</span>
                                        <span class="text-slate-200 font-medium">{{ $d->item->name ?? '-' }}</span>
                                    </div>
                                    @if ($d->fine)
                                        <span class="text-[11px] font-mono font-bold text-rose-400">
                                            Denda: Rp {{ number_format($d->fine->amount, 0, ',', '.') }} ({{ $d->fine->status }})
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Timeline & Status specific details -->
                    <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 text-xs space-y-2.5">
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Status Waktu & Instruksi</h4>

                        @if ($loan->status === 'pending')
                            <p class="text-amber-300/90 leading-relaxed">
                                Permintaan peminjaman sedang ditinjau kurator Admin Dream Pawn Shop. Anda akan mendapatkan tenggat serah-terima 24 jam setelah disetujui.
                            </p>
                        @elseif ($loan->status === 'approved')
                            <div class="space-y-1">
                                <p class="text-blue-300 font-semibold">Telah Disetujui! Unit props siap diambil di konter toko.</p>
                                <p class="text-amber-400 font-medium">
                                    Batas Pengambilan (24 Jam): {{ $loan->handover_deadline_at ? $loan->handover_deadline_at->format('d M Y, H:i') : '-' }}
                                </p>
                                <p class="text-slate-400 text-[11px]">Jika tidak diambil hingga tenggat, approval dibatalkan dan reservasi unit dilepas.</p>
                            </div>
                        @elseif ($loan->status === 'active')
                            <div class="space-y-1.5">
                                <div>Serah-terima fisik: <span class="text-slate-200">{{ $loan->handed_over_at ? $loan->handed_over_at->format('d M Y, H:i') : '-' }}</span></div>
                                <div class="text-sm font-semibold {{ $isOverdue ? 'text-rose-400' : 'text-emerald-400' }}">
                                    Jatuh Tempo Pengembalian: {{ $loan->due_at ? $loan->due_at->format('d M Y, H:i') : '-' }}
                                </div>
                                @if ($isOverdue)
                                    <div class="p-2.5 rounded-lg bg-rose-500/20 text-rose-200 border border-rose-500/30">
                                        Perkiraan Denda Keterlambatan: <strong>Rp {{ number_format($estFine, 0, ',', '.') }}</strong> (Rp 10.000 / hari penuh / unit). Segera kembalikan ke Admin.
                                    </div>
                                @else
                                    <p class="text-slate-400 text-[11px]">Harap kembalikan seluruh unit prop tepat waktu ke konter toko sebelum jatuh tempo.</p>
                                @endif
                            </div>
                        @elseif ($loan->status === 'returned')
                            <div class="text-purple-300">
                                Selesai dikembalikan pada: {{ $loan->returned_at ? $loan->returned_at->format('d M Y, H:i') : '-' }}
                            </div>
                            @if (($loan->total_fine ?? 0) > 0)
                                <div class="text-xs text-rose-400 font-mono">
                                    Total Denda: Rp {{ number_format($loan->total_fine, 0, ',', '.') }}
                                    @if (($loan->unpaid_fine ?? 0) > 0)
                                        <span class="text-rose-300 underline">(Belum Lunas: Rp {{ number_format($loan->unpaid_fine, 0, ',', '.') }})</span>
                                    @else
                                        <span class="text-emerald-400">(Telah Lunas)</span>
                                    @endif
                                </div>
                            @endif
                        @elseif ($loan->status === 'rejected')
                            <p class="text-rose-400">Pengajuan ini tidak disetujui kurator.</p>
                        @elseif ($loan->status === 'cancelled')
                            <p class="text-slate-500">Dibatalkan otomatis karena melewati batas waktu serah-terima 24 jam.</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            @include('components.empty-state', [
                'title' => 'Belum Ada Transaksi Pinjaman',
                'description' => 'Anda belum mengajukan peminjaman props dengan status ini.',
                'action' => 'Jelajahi Katalog Props',
                'actionUrl' => route('member.catalog.items.index')
            ])
        @endforelse
    </div>

    @include('components.pagination', ['paginator' => $loans])
</div>
@endsection
