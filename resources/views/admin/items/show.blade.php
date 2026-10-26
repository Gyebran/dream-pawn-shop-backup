@extends('layouts.admin')

@section('title', 'Detail Unit Props — Admin Dream Pawn Shop')
@section('page_title', 'Detail Unit Props')

@section('admin_content')
<div class="max-w-4xl mx-auto space-y-6">
    <a href="{{ route('admin.items.index') }}" class="text-xs text-slate-400 hover:text-amber-400">&larr; Kembali ke Katalog Props</a>
    <section class="glass-card rounded-2xl border border-slate-800 p-6 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <p class="text-xs font-mono text-amber-400">{{ $item->unit_code }}</p>
                <h1 class="font-serif-dream text-2xl font-bold text-white mt-1">{{ $item->name }}</h1>
            </div>
            @include('components.status-badge', ['status' => $item->status])
        </div>
        <div>
            <h2 class="text-xs uppercase tracking-wider text-slate-400 mb-2">Deskripsi</h2>
            <p class="text-sm text-slate-200 leading-relaxed">{{ $item->description ?: 'Belum ada deskripsi.' }}</p>
        </div>
        <div>
            <h2 class="text-xs uppercase tracking-wider text-slate-400 mb-2">Kategori</h2>
            <div class="flex flex-wrap gap-2">
                @forelse ($item->categories as $category)
                    <span class="px-3 py-1 rounded-lg bg-slate-800 border border-slate-700 text-xs text-amber-300">{{ $category->name }}</span>
                @empty
                    <span class="text-sm text-slate-500">Belum ada kategori.</span>
                @endforelse
            </div>
        </div>
        <div class="rounded-xl bg-slate-900/70 border border-slate-800 p-4 text-xs text-slate-400 space-y-1">
            <p><strong class="text-slate-200">Aturan sewa:</strong> Gratis selama 5 hari sejak serah-terima fisik.</p>
            <p><strong class="text-slate-200">Denda terlambat:</strong> Rp10.000 per hari penuh per unit.</p>
        </div>
        <a href="{{ route('admin.items.index') }}" class="inline-flex px-4 py-2 rounded-xl bg-slate-800 text-slate-200 text-xs font-semibold">Kembali ke Daftar Unit</a>
    </section>
</div>
@endsection
