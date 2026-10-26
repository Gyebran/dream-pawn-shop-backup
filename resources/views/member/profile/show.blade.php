@extends('layouts.member')

@section('title', 'Profil Saya — Dream Pawn Shop')

@section('member_content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <h1 class="font-serif-dream text-2xl font-bold text-white tracking-wide">Pengaturan Profil Akun</h1>
        <p class="text-xs text-slate-400 mt-1">Perbarui nama, email, atau ubah kata sandi akun Member Anda.</p>
    </div>

    <!-- Profile Info Card -->
    <div class="glass-card rounded-2xl p-8 border border-slate-800 shadow-xl">
        <form method="POST" action="{{ route('member.profile.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Name Field -->
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Lengkap *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? auth()->user()->name) }}" required
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-100 text-sm focus:border-amber-500 focus:outline-none transition">
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Alamat Email *</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? auth()->user()->email) }}" required
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-100 text-sm focus:border-amber-500 focus:outline-none transition">
            </div>

            <!-- Change Password Section -->
            <div class="pt-6 border-t border-slate-800 space-y-4">
                <h3 class="font-serif-dream text-base font-bold text-amber-200">Ganti Password (Opsional)</h3>
                <p class="text-xs text-slate-400">Kosongkan jika Anda tidak ingin mengubah password saat ini.</p>

                <div>
                    <label for="current_password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-100 text-sm focus:border-amber-500 focus:outline-none transition"
                           placeholder="Masukkan password saat ini jika ingin mengubah">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Password Baru</label>
                        <input type="password" id="password" name="password"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-100 text-sm focus:border-amber-500 focus:outline-none transition"
                               placeholder="Min. 8 karakter">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-100 text-sm focus:border-amber-500 focus:outline-none transition"
                               placeholder="Ketik ulang password baru">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end">
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm transition shadow-lg glow-gold">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
