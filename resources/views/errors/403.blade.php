@extends('layouts.app')

@section('title', '403 — Akses Ditolak — Dream Pawn Shop')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6 relative overflow-hidden bg-slate-950">
    <!-- Ambient Background Glow -->
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-rose-500/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-10 right-10 w-72 h-72 bg-amber-500/10 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="w-full max-w-lg relative z-10 text-center">
        <!-- Error Card -->
        <div class="glass-card rounded-3xl p-8 sm:p-10 border border-rose-500/30 shadow-2xl relative">
            <!-- Icon -->
            <div class="w-16 h-16 mx-auto rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center mb-6 shadow-lg shadow-rose-500/10">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m11-5a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <!-- Code & Title -->
            <span class="inline-block px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[11px] font-bold tracking-wider uppercase mb-3">
                HTTP 403 • Forbidden
            </span>
            <h1 class="font-serif-dream text-2xl sm:text-3xl font-bold text-white tracking-wide mb-3">
                {{ $title ?? 'Akses Ditolak' }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed mb-6">
                {{ $message ?? 'Anda tidak memiliki hak akses yang cukup untuk membuka halaman ini.' }}
            </p>

            @if (isset($currentUser))
                <!-- Current Session Box -->
                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 text-left mb-6 space-y-2">
                    <div class="text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Sesi Anda Saat Ini:</div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 text-amber-300 font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white">{{ $currentUser->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $currentUser->email }}</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $currentUser->role === 'admin' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' }}">
                            {{ $currentUser->role }}
                        </span>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                @if (isset($currentUser) && $currentUser->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-lg glow-gold flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Ke Dashboard Admin</span>
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit"
                                class="w-full px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-medium text-xs transition flex items-center justify-center gap-1.5">
                            <span>Beralih ke Akun Member</span>
                        </button>
                    </form>
                @elseif (isset($currentUser) && $currentUser->isMember())
                    <a href="{{ route('member.dashboard') }}"
                       class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-lg glow-gold flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Ke Dashboard Member</span>
                    </a>
                    <form method="POST" action="{{ route('member.logout') }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit"
                                class="w-full px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-medium text-xs transition flex items-center justify-center gap-1.5">
                            <span>Beralih ke Akun Admin</span>
                        </button>
                    </form>
                @else
                    <a href="{{ url('/') }}"
                       class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-lg glow-gold">
                        Kembali ke Beranda
                    </a>
                @endif
                <a href="{{ url('/') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-900/60 hover:bg-slate-800 text-slate-400 hover:text-slate-200 text-xs font-medium transition">
                    Beranda
                </a>
            </div>
        </div>

        <p class="text-[11px] text-slate-500 mt-6 font-serif-dream tracking-wider">
            Dream Pawn Shop • Sistem Manajemen & Rental Props Fisik Koleksi
        </p>
    </div>
</div>
@endsection
