@extends('layouts.admin')

@section('title', 'Detail Member — Admin Dream Pawn Shop')
@section('page_title', 'Detail Member')

@section('admin_content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('admin.members.index') }}" class="text-xs text-slate-400 hover:text-amber-400">&larr; Kembali ke Daftar Member</a>
    <section class="glass-card rounded-2xl border border-slate-800 p-6 space-y-5">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-2xl font-bold text-amber-300">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
            <div>
                <p class="text-xs font-mono text-slate-500">Member #{{ $member->id }}</p>
                <h1 class="font-serif-dream text-2xl font-bold text-white">{{ $member->name }}</h1>
                <p class="text-sm text-slate-400">{{ $member->email }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-xl bg-slate-900/70 border border-slate-800 p-4">
                <p class="text-xs text-slate-500 mb-1">Status Akun</p>
                <p class="text-sm font-semibold {{ $member->is_active ? 'text-emerald-300' : 'text-rose-300' }}">{{ $member->is_active ? 'Aktif' : 'Nonaktif' }}</p>
            </div>
            <div class="rounded-xl bg-slate-900/70 border border-slate-800 p-4">
                <p class="text-xs text-slate-500 mb-1">Terdaftar Sejak</p>
                <p class="text-sm font-semibold text-slate-200">{{ $member->created_at?->format('d M Y, H:i') ?? '-' }}</p>
            </div>
        </div>
        <p class="text-xs text-slate-500">Detail ini bersifat baca-saja. Perubahan profil dan status dapat dilakukan dari daftar Member.</p>
        <a href="{{ route('admin.members.index') }}" class="inline-flex px-4 py-2 rounded-xl bg-slate-800 text-slate-200 text-xs font-semibold">Kembali ke Daftar Member</a>
    </section>
</div>
@endsection
