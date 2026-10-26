@props(['title' => 'Tidak Ada Data', 'description' => 'Belum ada data yang tersedia untuk kriteria saat ini.', 'action' => null, 'actionUrl' => null])

<div class="py-12 px-4 text-center">
    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-800/60 border border-slate-700/50 flex items-center justify-center text-slate-400">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
    </div>
    <h3 class="text-lg font-medium text-slate-200 mb-1">{{ $title }}</h3>
    <p class="text-sm text-slate-400 max-w-sm mx-auto mb-5">{{ $description }}</p>
    @if ($action && $actionUrl)
        <a href="{{ $actionUrl }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium bg-amber-500 hover:bg-amber-400 text-slate-950 transition shadow-lg glow-gold">
            {{ $action }}
        </a>
    @endif
</div>
