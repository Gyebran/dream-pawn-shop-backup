@extends('layouts.admin')

@section('title', 'Detail Kategori — Admin Dream Pawn Shop')
@section('page_title', 'Detail Kategori')

@section('admin_content')
<div class="max-w-4xl mx-auto space-y-6">
    <a href="{{ route('admin.categories.index') }}" class="text-xs text-slate-400 hover:text-amber-400">&larr; Kembali ke Kategori</a>
    <section class="glass-card rounded-2xl border border-slate-800 p-6 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <p class="text-xs text-slate-500 font-mono">Kategori #{{ $category->id }}</p>
                <h1 class="font-serif-dream text-2xl font-bold text-white mt-1">{{ $category->name }}</h1>
            </div>
            @include('components.status-badge', ['status' => $category->is_archived ? 'archived' : 'active'])
        </div>
        <div>
            <h2 class="text-xs uppercase tracking-wider text-slate-400 mb-2">Deskripsi</h2>
            <p class="text-sm text-slate-200">{{ $category->description ?: 'Belum ada deskripsi.' }}</p>
        </div>
        <div class="border-t border-slate-800 pt-4">
            <h2 class="text-xs uppercase tracking-wider text-slate-400 mb-3">Unit Props dalam Kategori ({{ $category->items->count() }})</h2>
            @forelse ($category->items as $item)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 py-3 border-b border-slate-800/70 last:border-0">
                    <div>
                        <p class="text-sm font-semibold text-slate-100">{{ $item->name }}</p>
                        <p class="text-xs font-mono text-slate-500">{{ $item->unit_code }}</p>
                    </div>
                    <span class="text-xs text-slate-300">{{ ucfirst($item->status) }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">Belum ada unit yang terhubung dengan kategori ini.</p>
            @endforelse
        </div>
        <div class="flex flex-wrap gap-3 pt-2">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-200 text-xs font-semibold">Kembali ke Daftar</a>
            @if (!$category->is_archived)
                <form method="POST" action="{{ route('admin.categories.archive', $category) }}" onsubmit="return confirm('Arsipkan kategori ini?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-xl border border-rose-500/30 text-rose-300 text-xs font-semibold">Arsipkan Kategori</button>
                </form>
            @endif
        </div>
    </section>
</div>
@endsection
