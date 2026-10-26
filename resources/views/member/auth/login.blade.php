@extends('layouts.app')

@section('title', 'Member Login — Dream Pawn Shop')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6 relative">
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-cyan-600 via-sky-500 to-indigo-400 mx-auto flex items-center justify-center text-slate-950 font-bold shadow-2xl glow-cyan mb-4">
                <svg class="w-9 h-9 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <h1 class="font-serif-dream text-2xl font-bold text-white tracking-wider">Dream Pawn Shop</h1>
            <p class="text-xs text-cyan-400 font-semibold uppercase tracking-widest mt-1">Portal Peminjaman Member</p>
        </div>

        <!-- Login Card -->
        <div class="glass-card rounded-2xl p-8 border border-slate-700/60 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-24 -left-24 w-48 h-48 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>

            @include('components.flash')

            <form method="POST" action="{{ route('member.login') }}" class="space-y-5" id="member-login-form">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Email Terdaftar</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </span>
                        <input type="email" id="email" name="email" value="{{ old('email', app()->environment('local', 'testing') ? 'member@dreampawn.test' : '') }}" required autofocus
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-900/80 border border-slate-700 text-slate-100 text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition placeholder-slate-500"
                               placeholder="member@dreampawn.test">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <input type="password" id="password" name="password" required
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-900/80 border border-slate-700 text-slate-100 text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition placeholder-slate-500"
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="btn-submit"
                            class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-cyan-500 via-sky-500 to-indigo-500 hover:from-cyan-400 hover:to-indigo-400 text-slate-950 font-bold text-sm tracking-wide transition transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg glow-cyan flex items-center justify-center gap-2">
                        <span>Masuk Portal Member</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>

            @if (app()->environment('local', 'testing'))
            <!-- Quick Demo Credential Autofill (local/test only) -->
            <div class="mt-6 pt-6 border-t border-slate-800 text-center">
                <p class="text-xs text-slate-400 mb-2">Akun Uji Coba Lokal (Demo):</p>
                <button type="button" onclick="fillMemberDemo()" 
                        class="px-3 py-1.5 rounded-lg bg-slate-800/80 hover:bg-slate-800 text-cyan-300 text-xs border border-cyan-500/30 transition flex items-center gap-1.5 mx-auto">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Klik Isi Otomatis Akun Demo Member</span>
                </button>
            </div>
            @endif
        </div>

        <div class="mt-6 text-center text-sm text-slate-400">
            Belum punya akun?
            <a href="{{ route('member.register') }}" class="text-cyan-400 hover:text-cyan-300 font-semibold transition">Daftar sebagai Member</a>
        </div>

        <div class="mt-4 text-center text-xs text-slate-400">
            <a href="{{ route('home') }}" class="hover:text-cyan-400 transition">&larr; Kembali ke Halaman Utama</a>
            <span class="mx-2 text-slate-600">•</span>
            <a href="{{ route('admin.login.view') }}" class="hover:text-amber-400 transition">Command Center Admin &rarr;</a>
        </div>
    </div>
</div>

@if (app()->environment('local', 'testing'))
<script>
    function fillMemberDemo() {
        document.getElementById('email').value = 'member@dreampawn.test';
        document.getElementById('password').value = 'MemberDemo123!';
        showToast('Kredensial Demo Member telah diisi!', 'success');
    }
</script>
@endif
@endsection
