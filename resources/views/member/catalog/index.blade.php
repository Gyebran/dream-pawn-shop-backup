@extends('layouts.member')

@section('title', 'Katalog Props Surreal — Dream Pawn Shop')

@section('member_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif-dream text-2xl font-bold text-white tracking-wide">Koleksi Artefak & Props Surreal</h1>
            <p class="text-xs text-slate-400 mt-1">
                Pilih hingga 2 unit fisik props untuk dipinjam gratis selama 5 hari kalender.
            </p>
        </div>

        <!-- Basket summary counter if user selects items -->
        <div id="selection-bar" class="hidden items-center gap-3 px-4 py-2.5 rounded-xl bg-amber-500/15 border border-amber-500/30 text-xs">
            <span class="text-slate-300">Dipilih: <strong id="selected-count" class="text-amber-400 font-bold text-sm">0</strong> / 2 Unit</span>
            <button type="button" onclick="openBorrowModal()" 
                    class="px-4 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow glow-gold">
                Ajukan Pinjaman Sekarang &rarr;
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <form method="GET" action="{{ route('member.catalog.items.index') }}" class="glass-card rounded-2xl p-4 border border-slate-800 flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[240px] relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama atau kode props..."
                   class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-900/80 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-amber-500 transition placeholder-slate-500">
        </div>

        <div class="w-44">
            <select name="category_id" class="w-full px-3 py-2 rounded-xl bg-slate-900/80 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-amber-500">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
            <input type="checkbox" name="available" value="1" {{ !empty($filters['available']) ? 'checked' : '' }}
                   class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-amber-500/20">
            <span>Hanya Yang Tersedia</span>
        </label>

        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 transition">
            Filter
        </button>
        @if (!empty($filters['search']) || !empty($filters['category_id']) || !empty($filters['available']))
            <a href="{{ route('member.catalog.items.index') }}" class="text-xs text-rose-400 hover:underline">Reset</a>
        @endif
    </form>

    <!-- Props Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($items as $item)
            @php
                $isAvail = $item->status === 'available';
            @endphp
            <div class="glass-card rounded-2xl p-6 border border-slate-800 flex flex-col justify-between relative group hover:border-amber-500/30 transition duration-300 {{ !$isAvail ? 'opacity-70' : '' }}">
                <div>
                    @if ($item->image_path)
                        <img src="{{ asset('storage/'.$item->image_path) }}" alt="Foto {{ $item->name }}" loading="lazy" class="mb-4 h-48 w-full rounded-xl border border-slate-700 object-cover">
                    @else
                        <div class="mb-4 flex h-48 w-full items-center justify-center rounded-xl border border-dashed border-slate-700 bg-slate-900/60 text-xs text-slate-500">Foto belum tersedia</div>
                    @endif

                    <!-- Top row: Unit code & status -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="font-mono text-xs font-bold text-amber-400 px-2 py-0.5 rounded bg-amber-500/10 border border-amber-500/20">
                            {{ $item->unit_code }}
                        </span>
                        @include('components.status-badge', ['status' => $item->status])
                    </div>

                    <!-- Prop Name -->
                    <h3 class="font-serif-dream text-lg font-bold text-white mb-2 group-hover:text-amber-200 transition">
                        {{ $item->name }}
                    </h3>

                    <!-- Categories pills -->
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        @foreach ($item->categories as $c)
                            <span class="px-2 py-0.5 rounded-md bg-slate-800 border border-slate-700/80 text-[10px] text-slate-300 font-medium">
                                {{ $c->name }}
                            </span>
                        @endforeach
                    </div>

                    <!-- Description -->
                    <p class="text-xs text-slate-400 leading-relaxed line-clamp-3 mb-4">
                        {{ $item->description ?? 'Artefak surreal tanpa deskripsi khusus.' }}
                    </p>
                </div>

                <!-- Action Button -->
                <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    @if ($isAvail)
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-200 hover:text-amber-300 transition select-none">
                            <input type="checkbox" value="{{ $item->id }}" 
                                   data-code="{{ $item->unit_code }}" 
                                   data-name="{{ $item->name }}"
                                   onchange="toggleSelectProp(this)"
                                   class="prop-checkbox rounded w-4 h-4 bg-slate-900 border-slate-600 text-amber-500 focus:ring-amber-500/20">
                            <span>Pilih untuk Dipinjam</span>
                        </label>
                    @else
                        <span class="text-xs text-slate-500 italic">
                            {{ $item->status === 'reserved' ? 'Sedang direservasi member lain' : 'Sedang dipinjam' }}
                        </span>
                    @endif

                    <a href="{{ route('member.catalog.items.show', $item->id) }}" class="text-xs text-slate-400 hover:text-amber-400 transition font-medium">
                        Detail &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                @include('components.empty-state', [
                    'title' => 'Tidak Ada Koleksi Yang Cocok',
                    'description' => 'Coba ubah kata kunci pencarian atau reset filter kategori.'
                ])
            </div>
        @endforelse
    </div>

    @include('components.pagination', ['paginator' => $items])
</div>

<!-- Modal Borrow Confirmation -->
<div id="borrow-modal" class="modal-wrapper fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 modal-backdrop" onclick="closeModal('borrow-modal')"></div>
    <div class="relative glass-card modal-content rounded-2xl border border-amber-500/30 p-6 max-w-md w-full shadow-2xl z-10">
        <div class="flex items-center gap-3 text-amber-400 mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <h3 class="font-serif-dream text-base font-bold text-white">Ajukan Peminjaman Props</h3>
        </div>

        <p class="text-xs text-slate-300 mb-4 leading-relaxed">
            Anda akan mengajukan peminjaman untuk unit props berikut:
        </p>

        <!-- Selected list in modal -->
        <div id="modal-selected-list" class="space-y-2 mb-4 p-3 rounded-xl bg-slate-900 border border-slate-800 text-xs"></div>

        <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-300/90 mb-5 space-y-1">
            <p><strong>Syarat & Ketentuan:</strong></p>
            <ul class="list-disc list-inside space-y-0.5 text-[11px] text-slate-300">
                <li>Setelah Admin menyetujui, Anda memiliki <strong>24 jam</strong> untuk serah-terima fisik di toko.</li>
                <li>Masa sewa gratis berlangsung selama <strong>5 hari kalender</strong> sejak serah-terima.</li>
                <li>Keterlambatan dikenakan denda Rp 10.000 / hari keterlambatan / unit.</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('member.loan-requests.store') }}" id="borrow-form">
            @csrf
            <div id="hidden-inputs"></div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('borrow-modal')" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-lg glow-gold">
                    Kirim Pengajuan Pinjaman
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let selectedProps = [];

    function toggleSelectProp(cb) {
        const id = parseInt(cb.value);
        const name = cb.getAttribute('data-name');
        const code = cb.getAttribute('data-code');

        if (cb.checked) {
            if (selectedProps.length >= 2) {
                cb.checked = false;
                showToast('Maksimal peminjaman adalah 2 unit props sekaligus!', 'error');
                return;
            }
            selectedProps.push({ id, name, code });
        } else {
            selectedProps = selectedProps.filter(p => p.id !== id);
        }

        updateSelectionBar();
    }

    function updateSelectionBar() {
        const bar = document.getElementById('selection-bar');
        const count = document.getElementById('selected-count');
        if (selectedProps.length > 0) {
            bar.classList.remove('hidden');
            bar.classList.add('flex');
            count.textContent = selectedProps.length;
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
        }
    }

    function openBorrowModal() {
        if (selectedProps.length === 0) {
            showToast('Pilih setidaknya 1 prop yang tersedia!', 'error');
            return;
        }

        const list = document.getElementById('modal-selected-list');
        const hidden = document.getElementById('hidden-inputs');
        list.innerHTML = '';
        hidden.innerHTML = '';

        selectedProps.forEach(p => {
            list.innerHTML += `<div class="flex items-center justify-between text-slate-200">
                <span class="font-medium">${p.name}</span>
                <span class="font-mono text-amber-400">[${p.code}]</span>
            </div>`;

            hidden.innerHTML += `<input type="hidden" name="item_ids[]" value="${p.id}">`;
        });

        openModal('borrow-modal');
    }
</script>
@endsection
