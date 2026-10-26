@extends('layouts.member')

@section('title', 'Member Dashboard — Dream Pawn Shop')

@section('member_content')
<div class="space-y-8">
    <!-- Top Welcome & Eligibility Banner -->
    <div class="relative overflow-hidden rounded-2xl glass-card p-8 border border-slate-800 shadow-xl">
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs uppercase tracking-widest text-cyan-400 font-semibold font-mono">Member Sanctuary</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-xs text-slate-400">ID Member #{{ $data['member']['id'] }}</span>
                </div>
                <h1 class="font-serif-dream text-2xl md:text-3xl font-bold text-white tracking-wide">
                    Selamat Datang, {{ $data['member']['name'] }}
                </h1>
                <p class="text-sm text-slate-400 mt-1 max-w-2xl leading-relaxed">
                    Sistem peminjaman artefak surreal fisik Dream Pawn Shop. Anda dapat meminjam maksimal 2 unit sekaligus secara gratis untuk durasi 5 hari kalender.
                </p>
            </div>

            <div class="shrink-0 flex items-center gap-3">
                @if ($data['eligibility']['can_request'])
                    <a href="{{ route('member.catalog.items.index') }}" 
                       class="px-5 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm transition shadow-lg glow-gold flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>Pinjam Props Koleksi</span>
                    </a>
                @else
                    <button disabled 
                            class="px-5 py-3 rounded-xl bg-slate-800 text-slate-500 font-bold text-sm cursor-not-allowed flex items-center gap-2 border border-slate-700">
                        <span>Peminjaman Dinonaktifkan</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Eligibility Status Ribbon -->
        <div class="mt-6 pt-6 border-t border-slate-800/80">
            @if ($data['eligibility']['can_request'])
                <div class="flex items-center gap-3 text-xs text-emerald-300 bg-emerald-950/40 border border-emerald-500/30 px-4 py-2.5 rounded-xl">
                    <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><strong>Status Memenuhi Syarat:</strong> Anda tidak memiliki denda tertunggak dan memiliki sisa kuota pinjaman (<strong>{{ $data['allocation']['remaining_units'] }} unit</strong> tersedia).</span>
                </div>
            @else
                <div class="flex items-start gap-3 text-xs text-rose-300 bg-rose-950/40 border border-rose-500/30 px-4 py-3 rounded-xl">
                    <svg class="w-4 h-4 text-rose-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <strong class="text-rose-200">Tidak Memenuhi Syarat Peminjaman Saat Ini:</strong>
                        <ul class="list-disc list-inside mt-1 space-y-0.5 text-rose-300/90">
                            @foreach ($data['eligibility']['reasons'] as $r)
                                <li>{{ $r }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Allocation & Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Remaining Allocation -->
        <div class="glass-card rounded-2xl p-6 border border-slate-800 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Sisa Kuota Pinjam</span>
                <span class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center font-bold text-xs">
                    {{ $data['allocation']['remaining_units'] }}/2
                </span>
            </div>
            <div>
                <p class="text-3xl font-bold text-amber-300 font-mono">{{ $data['allocation']['remaining_units'] }} <span class="text-sm font-normal text-slate-400">Unit</span></p>
                <p class="text-xs text-slate-400 mt-1">Batas maksimal: 2 unit props</p>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-800/80 text-[11px] text-slate-400">
                <span>Dipinjam: {{ $data['allocation']['allocated_units'] }} • Menunggu: {{ $data['allocation']['pending_units'] }}</span>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="glass-card rounded-2xl p-6 border border-slate-800 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pinjaman Aktif</span>
                <span class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-400 border border-teal-500/20 flex items-center justify-center font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div>
                <p class="text-3xl font-bold text-teal-300 font-mono">{{ $data['requests']['active'] }}</p>
                <p class="text-xs text-slate-400 mt-1">
                    @if ($data['requests']['overdue'] > 0)
                        <span class="text-rose-400 font-bold">{{ $data['requests']['overdue'] }} unit terlambat!</span>
                    @else
                        Masa peminjaman 5 hari
                    @endif
                </p>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-800/80 text-[11px]">
                <a href="{{ route('member.loan-requests.index', ['status' => 'active']) }}" class="text-amber-400 hover:text-amber-300">Lihat Pinjaman Aktif &rarr;</a>
            </div>
        </div>

        <!-- Pending Approval -->
        <div class="glass-card rounded-2xl p-6 border border-slate-800 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Menunggu Review</span>
                <span class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 border border-sky-500/20 flex items-center justify-center font-bold text-xs">
                    {{ $data['requests']['pending'] }}
                </span>
            </div>
            <div>
                <p class="text-3xl font-bold text-sky-300 font-mono">{{ $data['requests']['pending'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Dalam antrean verifikasi Admin</p>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-800/80 text-[11px]">
                <a href="{{ route('member.loan-requests.index', ['status' => 'pending']) }}" class="text-amber-400 hover:text-amber-300">Cek Status Pengajuan &rarr;</a>
            </div>
        </div>

        <!-- Unpaid Fines -->
        <div class="glass-card rounded-2xl p-6 border border-slate-800 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Denda Tertunggak</span>
                <span class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 border border-rose-500/20 flex items-center justify-center font-bold text-xs">
                    {{ $data['fines']['unpaid_count'] }}
                </span>
            </div>
            <div>
                <p class="text-2xl font-bold font-mono {{ $data['fines']['unpaid_amount'] > 0 ? 'text-rose-400' : 'text-slate-300' }}">
                    Rp {{ number_format($data['fines']['unpaid_amount'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-400 mt-1">
                    {{ $data['fines']['unpaid_count'] > 0 ? $data['fines']['unpaid_count'].' denda belum diselesaikan' : 'Bersih dari denda' }}
                </p>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-800/80 text-[11px]">
                <a href="{{ route('member.fines.index') }}" class="text-amber-400 hover:text-amber-300">Rincian Denda & Pelunasan &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Quick Workflow Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Explore Catalog -->
        <div class="glass-card rounded-2xl p-6 border border-slate-800 hover:border-amber-500/40 transition">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <h3 class="font-serif-dream text-lg font-bold text-white mb-2">Jelajahi Koleksi Props Surreal</h3>
            <p class="text-xs text-slate-400 mb-5 leading-relaxed">
                Pilih hingga 2 unit artefak seperti Jam Kemarin, Kotak Musik Tanpa Nada, atau Kunci Tanpa Pintu. Seluruh sewa prop fisik gratis selama 5 hari kalender.
            </p>
            <a href="{{ route('member.catalog.items.index') }}" 
               class="inline-flex items-center gap-2 text-xs font-bold text-amber-400 hover:text-amber-300 transition">
                <span>Buka Katalog Lengkap</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <!-- Card 2: Return Instructions -->
        <div class="glass-card rounded-2xl p-6 border border-slate-800 hover:border-cyan-500/40 transition">
            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            </div>
            <h3 class="font-serif-dream text-lg font-bold text-white mb-2">Panduan Pengembalian Props</h3>
            <p class="text-xs text-slate-400 mb-5 leading-relaxed">
                Untuk mengembalikan props, bawa seluruh unit fisik ke konter Dream Pawn Shop. Admin akan memeriksa kondisi prop dan mengonfirmasi pengembalian secara serentak.
            </p>
            <a href="{{ route('member.loan-requests.index') }}" 
               class="inline-flex items-center gap-2 text-xs font-bold text-cyan-400 hover:text-cyan-300 transition">
                <span>Cek Tenggat Pinjaman Saya</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</div>
@endsection
