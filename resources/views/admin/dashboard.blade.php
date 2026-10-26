@extends('layouts.admin')

@section('title', 'Admin Dashboard — Dream Pawn Shop')
@section('page_title', 'Ringkasan Dashboard')

@section('admin_content')
<div class="space-y-8">
    <!-- Top Greeting Banner -->
    <div class="relative overflow-hidden rounded-2xl glass-card p-8 border border-amber-500/20 shadow-xl">
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-300 border border-amber-500/30 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                    Sistem Operasional Aktif
                </span>
                <h1 class="font-serif-dream text-2xl md:text-3xl font-bold text-white tracking-wide">
                    Selamat Datang di Command Center
                </h1>
                <p class="text-sm text-slate-400 mt-1 max-w-2xl">
                    Kelola props koleksi fantasi surreal, review pengajuan peminjaman (aturan maksimal 2 unit), pantau serah-terima 24 jam, tenggat 5 hari, serta konfirmasi pengembalian dan denda.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.loan-requests.index') }}" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm transition shadow-lg glow-gold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Tinjau Request ({{ $summary['requests_pending'] }})</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert If Pending Requests or Unpaid Fines require action -->
    @if ($summary['requests_pending'] > 0 || $summary['unpaid_fines_count'] > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if ($summary['requests_pending'] > 0)
                <div class="p-4 rounded-xl bg-amber-950/40 border border-amber-500/30 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-amber-400 font-semibold uppercase tracking-wider">Perlu Tindakan</p>
                            <p class="text-sm font-medium text-slate-200"><span class="text-amber-300 font-bold">{{ $summary['requests_pending'] }}</span> Pengajuan Pinjaman Menunggu Review</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.loan-requests.index', ['status' => 'pending']) }}" class="text-xs text-amber-400 hover:text-amber-300 underline font-medium">Buka &rarr;</a>
                </div>
            @endif

            @if ($summary['unpaid_fines_count'] > 0)
                <div class="p-4 rounded-xl bg-rose-950/40 border border-rose-500/30 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-rose-400 font-semibold uppercase tracking-wider">Denda Belum Lunas</p>
                            <p class="text-sm font-medium text-slate-200"><span class="text-rose-300 font-bold">{{ $summary['unpaid_fines_count'] }}</span> denda (Rp {{ number_format($summary['unpaid_fines_amount'], 0, ',', '.') }})</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.fines.index', ['status' => 'unpaid']) }}" class="text-xs text-rose-400 hover:text-rose-300 underline font-medium">Buka &rarr;</a>
                </div>
            @endif
        </div>
    @endif

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Available Props -->
        <div class="glass-card rounded-2xl p-6 border border-slate-800 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Props Tersedia</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <div>
                <p class="text-3xl font-bold text-white">{{ $summary['items_available'] }} <span class="text-sm font-normal text-slate-400">/ {{ $summary['items_total'] }} unit</span></p>
                <p class="text-xs text-slate-400 mt-1">Siap untuk dipinjam member</p>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <a href="{{ route('admin.items.index') }}" class="text-amber-400 hover:text-amber-300 font-medium">Lihat Semua Props &rarr;</a>
                <span class="text-slate-500">{{ $summary['categories_active'] }} Kategori</span>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="glass-card rounded-2xl p-6 border border-slate-800 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Menunggu Review</span>
                <span class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div>
                <p class="text-3xl font-bold text-amber-300">{{ $summary['requests_pending'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Reservasi unit otomatis saat diapprove</p>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <a href="{{ route('admin.loan-requests.index') }}" class="text-amber-400 hover:text-amber-300 font-medium">Buka Antrean &rarr;</a>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="glass-card rounded-2xl p-6 border border-slate-800 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pinjaman Aktif</span>
                <span class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-400 border border-sky-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
            </div>
            <div>
                <p class="text-3xl font-bold text-sky-300">{{ $summary['loans_active'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Durasi 5 hari kalender</p>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <a href="{{ route('admin.loan-requests.index', ['status' => 'active']) }}" class="text-amber-400 hover:text-amber-300 font-medium">Cek Jatuh Tempo &rarr;</a>
            </div>
        </div>

        <!-- Active Members -->
        <div class="glass-card rounded-2xl p-6 border border-slate-800 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Member Aktif</span>
                <span class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div>
                <p class="text-3xl font-bold text-purple-300">{{ $summary['members_active'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Maks. 2 unit per akun</p>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <a href="{{ route('admin.members.index') }}" class="text-amber-400 hover:text-amber-300 font-medium">Kelola Akun &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Quick Workflow Guides / System Rules -->
    <div class="glass-card rounded-2xl p-6 border border-slate-800">
        <h3 class="font-serif-dream text-base font-bold text-amber-200 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Ringkasan Regulasi Bisnis Dream Pawn Shop</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs text-slate-300">
            <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                <div class="flex items-center gap-2 text-amber-400 font-bold mb-2">
                    <span class="w-6 h-6 rounded-lg bg-amber-500/10 flex items-center justify-center">1</span>
                    <span>Reservasi & Batas 24 Jam</span>
                </div>
                <p class="leading-relaxed text-slate-400">
                    Approval mereservasi seluruh unit secara atomik. Serah-terima fisik harus selesai dalam 24 jam. Jika lewat, scheduler membatalkan approval dan melepas reservasi.
                </p>
            </div>

            <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                <div class="flex items-center gap-2 text-sky-400 font-bold mb-2">
                    <span class="w-6 h-6 rounded-lg bg-sky-500/10 flex items-center justify-center">2</span>
                    <span>Durasi 5 Hari & Denda</span>
                </div>
                <p class="leading-relaxed text-slate-400">
                    Durasi rental 5 hari kalender dimulai saat serah-terima fisik aktual (<code class="text-sky-300">handed_over_at</code>). Denda keterlambatan Rp 10.000 / hari penuh / unit.
                </p>
            </div>

            <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                <div class="flex items-center gap-2 text-purple-400 font-bold mb-2">
                    <span class="w-6 h-6 rounded-lg bg-purple-500/10 flex items-center justify-center">3</span>
                    <span>Pengembalian & Pelunasan</span>
                </div>
                <p class="leading-relaxed text-slate-400">
                    Member menghubungi Admin untuk fisik return; Admin mengonfirmasi seluruh unit sekaligus. Member dengan denda unpaid tidak dapat mengajukan loan baru.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
