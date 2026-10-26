@extends('layouts.member')

@section('title', 'Kategori Props — Dream Pawn Shop')

@section('member_content')
<div class="space-y-6">
    <div>
        <h1 class="font-serif-dream text-2xl font-bold text-white">Kategori Koleksi Props</h1>
        <p class="text-sm text-slate-400 mt-1">Jelajahi artefak berdasarkan kategori koleksi.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($categories as $category)
            <a href="{{ route('member.catalog.items.index', ['category_id' => $category->id]) }}" class="glass-card rounded-2xl border border-slate-800 p-5 hover:border-amber-500/40 transition">
                <p class="text-xs font-mono text-amber-400 mb-2">KATEGORI #{{ $category->id }}</p>
                <h2 class="font-serif-dream text-lg font-bold text-white">{{ $category->name }}</h2>
                <p class="text-sm text-slate-400 mt-2">{{ $category->description ?: 'Koleksi props tanpa deskripsi tambahan.' }}</p>
                <span class="inline-flex mt-4 text-xs font-semibold text-amber-300">Lihat props &rarr;</span>
            </a>
        @empty
            @include('components.empty-state', ['title' => 'Belum Ada Kategori', 'description' => 'Kategori koleksi belum tersedia.'])
        @endforelse
    </div>
    <a href="{{ route('member.catalog.items.index') }}" class="inline-flex px-4 py-2 rounded-xl bg-slate-800 text-slate-200 text-xs font-semibold">Lihat Semua Props</a>
</div>
@endsection
