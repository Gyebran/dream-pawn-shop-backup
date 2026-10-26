@props(['paginator'])

@if ($paginator->hasPages())
    <div class="px-6 py-4 flex items-center justify-between border-t border-slate-800/80 bg-slate-900/40 text-sm text-slate-400">
        <div>
            Menampilkan <span class="text-amber-400 font-medium">{{ $paginator->firstItem() ?? 0 }}</span> - <span class="text-amber-400 font-medium">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="text-white font-medium">{{ $paginator->total() }}</span> data
        </div>

        <div class="flex items-center gap-1.5">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg border border-slate-800 bg-slate-900/50 text-slate-600 cursor-not-allowed">
                    &larr; Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 text-slate-200 transition">
                    &larr; Sebelumnya
                </a>
            @endif

            {{-- Current Page indicator --}}
            <span class="px-3 py-1.5 rounded-lg border border-amber-500/30 bg-amber-500/10 text-amber-300 font-medium">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 text-slate-200 transition">
                    Berikutnya &rarr;
                </a>
            @else
                <span class="px-3 py-1.5 rounded-lg border border-slate-800 bg-slate-900/50 text-slate-600 cursor-not-allowed">
                    Berikutnya &rarr;
                </span>
            @endif
        </div>
    </div>
@endif
