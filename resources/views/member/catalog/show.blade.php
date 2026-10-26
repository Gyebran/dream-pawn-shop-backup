@extends('layouts.member')

@section('title', $item->name . ' — Dream Pawn Shop')

@section('member_content')
<div class="max-w-4xl mx-auto space-y-6">
    <a href="{{ route('member.catalog.items.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-amber-400 transition">
        &larr; Kembali ke Katalog Props
    </a>

    <div class="glass-card rounded-2xl p-8 border border-slate-800 shadow-2xl relative overflow-hidden">
        <div class="flex flex-col md:flex-row gap-8">
            <div class="w-full md:w-1/3 aspect-square rounded-2xl bg-gradient-to-tr from-slate-900 to-slate-800 border border-slate-700/80 overflow-hidden shrink-0">
                @if ($item->image_path)
                    <img src="{{ asset('storage/'.$item->image_path) }}" alt="Foto {{ $item->name }}" class="h-full w-full object-cover" fetchpriority="high">
                @else
                    <div class="h-full w-full flex items-center justify-center p-8 text-amber-400/80">
                        <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"/>
                        </svg>
                    </div>
                @endif
            </div>

            <div class="flex-1 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <span class="font-mono text-sm font-bold text-amber-400 px-3 py-1 rounded-lg bg-amber-500/10 border border-amber-500/30">
                        {{ $item->unit_code }}
                    </span>
                    @include('components.status-badge', ['status' => $item->status])
                </div>

                <h1 class="font-serif-dream text-2xl md:text-3xl font-bold text-white tracking-wide">
                    {{ $item->name }}
                </h1>

                <!-- Categories -->
                <div class="flex flex-wrap gap-2">
                    @foreach ($item->categories as $c)
                        <span class="px-3 py-1 rounded-lg bg-slate-800 border border-slate-700 text-xs text-amber-300 font-medium">
                            {{ $c->name }}
                        </span>
                    @endforeach
                </div>

                <!-- Description -->
                <div class="pt-4 border-t border-slate-800">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Deskripsi & Catatan Kurator</h4>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        {{ $item->description ?? 'Artefak surreal tanpa deskripsi khusus.' }}
                    </p>
                </div>

                <!-- Rental Conditions Box -->
                <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800 text-xs space-y-1 text-slate-400">
                    <p><strong class="text-slate-200">Durasi Peminjaman:</strong> 5 hari kalender gratis sejak serah-terima fisik.</p>
                    <p><strong class="text-slate-200">Tenggat Handover:</strong> 24 jam setelah persetujuan Admin.</p>
                    <p><strong class="text-slate-200">Denda Keterlambatan:</strong> Rp 10.000 / hari keterlambatan penuh / unit.</p>
                </div>

                @if ($item->status === 'available')
                    <div class="pt-4">
                        <form method="POST" action="{{ route('member.loan-requests.store') }}">
                            @csrf
                            <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                            <button type="submit" 
                                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm transition shadow-lg glow-gold flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                <span>Ajukan Pinjaman Unit Ini</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="pt-4">
                        <button disabled class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 text-slate-500 font-bold text-sm cursor-not-allowed border border-slate-700">
                            Unit Sedang Tidak Tersedia ({{ ucfirst($item->status) }})
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
