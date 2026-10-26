@extends('layouts.app')

@section('title', 'Registrasi Member — Dream Pawn Shop')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6 relative">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-cyan-600 via-sky-500 to-indigo-400 mx-auto flex items-center justify-center text-slate-950 font-bold shadow-2xl mb-4">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <h1 class="font-serif-dream text-2xl font-bold text-white tracking-wider">Buat Akun Member</h1>
            <p class="text-xs text-cyan-400 font-semibold uppercase tracking-widest mt-1">Bergabung dengan Dream Pawn Shop</p>
        </div>

        <div class="glass-card rounded-2xl p-8 border border-slate-700/60 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-24 -left-24 w-48 h-48 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>
            @include('components.flash')

            <form method="POST" action="{{ route('member.register.store') }}" class="space-y-5" id="member-register-form">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900/80 border border-slate-700 text-slate-100 text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition"
                           placeholder="Nama lengkap">
                    @error('name')<p class="mt-2 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900/80 border border-slate-700 text-slate-100 text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition"
                           placeholder="nama@email.com">
                    @error('email')<p class="mt-2 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900/80 border border-slate-700 text-slate-100 text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition"
                           placeholder="Minimal 8 karakter">
                    @error('password')<p class="mt-2 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900/80 border border-slate-700 text-slate-100 text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition"
                           placeholder="Ulangi password">
                </div>

                <button type="submit"
                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-cyan-500 via-sky-500 to-indigo-500 hover:from-cyan-400 hover:to-indigo-400 text-slate-950 font-bold text-sm tracking-wide transition shadow-lg">
                    Buat Akun Member
                </button>
            </form>
        </div>

        <div class="mt-6 text-center text-sm text-slate-400">
            Sudah punya akun?
            <a href="{{ route('member.login.view') }}" class="text-cyan-400 hover:text-cyan-300 font-semibold transition">Masuk di sini</a>
        </div>
        <div class="mt-3 text-center text-xs text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-cyan-400 transition">&larr; Kembali ke Halaman Utama</a>
        </div>
    </div>
</div>
@endsection
