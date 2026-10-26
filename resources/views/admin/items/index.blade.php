@extends('layouts.admin')

@section('title', 'Katalog Props Fisik — Admin Dream Pawn Shop')
@section('page_title', 'Kelola Barang Props')

@section('admin_content')
<div class="space-y-6">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif-dream text-xl font-bold text-white tracking-wide">Katalog Barang Props Fisik</h1>
            <p class="text-xs text-slate-400 mt-1">Seluruh unit fisik koleksi surreal yang disewakan secara gratis (maks 2 unit/member).</p>
        </div>
        <button type="button" onclick="openModal('create-item-modal')"
                class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm transition shadow-lg glow-gold flex items-center justify-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Unit Prop Baru</span>
        </button>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('admin.items.index') }}" class="glass-card rounded-2xl p-4 border border-slate-800 flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[240px] relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama atau kode unit prop..."
                   class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-900/80 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-amber-500 transition placeholder-slate-500">
        </div>

        <div class="w-44">
            <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-900/80 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-amber-500">
                <option value="">Semua Status</option>
                <option value="available" {{ ($filters['status'] ?? '') === 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                <option value="reserved" {{ ($filters['status'] ?? '') === 'reserved' ? 'selected' : '' }}>Direservasi (Reserved)</option>
                <option value="loaned" {{ ($filters['status'] ?? '') === 'loaned' ? 'selected' : '' }}>Dipinjam (Loaned)</option>
                <option value="archived" {{ ($filters['status'] ?? '') === 'archived' ? 'selected' : '' }}>Diarsipkan (Archived)</option>
            </select>
        </div>

        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 transition">
            Filter
        </button>
        @if (!empty($filters['search']) || !empty($filters['status']))
            <a href="{{ route('admin.items.index') }}" class="text-xs text-rose-400 hover:underline">Reset</a>
        @endif
    </form>

    <!-- Items Table -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Kode Unit</th>
                        <th class="px-6 py-4">Nama Props</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse ($items as $item)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-mono font-bold text-amber-400">
                                {{ $item->unit_code }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-100 text-sm block">{{ $item->name }}</span>
                                @if ($item->image_path)
                                    <img src="{{ asset('storage/'.$item->image_path) }}" alt="Foto {{ $item->name }}" loading="lazy" class="mt-2 h-12 w-16 rounded-lg border border-slate-700 object-cover">
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($item->categories as $c)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-800 border border-slate-700 text-[10px] text-amber-300">
                                            {{ $c->name }}
                                        </span>
                                    @empty
                                        <span class="text-slate-500 italic">-</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-400 max-w-xs truncate">{{ $item->description ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @include('components.status-badge', ['status' => $item->status])
                                @if (in_array($item->status, ['reserved', 'loaned']))
                                    <span class="block text-[10px] text-slate-500 mt-1">Terkunci dalam transaksi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <button type="button" 
                                        onclick="openEditItem({{ json_encode($item) }}, {{ json_encode($item->categories->pluck('id')) }})"
                                        class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-300 border border-slate-700 text-[11px] font-medium transition">
                                    Edit
                                </button>
                                @if ($item->status === 'available')
                                    <button type="button" 
                                            onclick="openArchiveItem({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->unit_code }}')"
                                            class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/20 text-[11px] font-medium transition">
                                        Arsipkan
                                    </button>
                                @elseif ($item->status !== 'archived')
                                    <span class="text-[10px] text-slate-600 italic cursor-not-allowed" title="Unit sedang reserved/loaned dan tidak bisa diarsipkan">
                                        Sedang Dipakai
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                @include('components.empty-state', [
                                    'title' => 'Props Tidak Ditemukan',
                                    'description' => 'Belum ada barang props yang cocok dengan kriteria pencarian.'
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $items])
    </div>
</div>

<!-- Modal Create Item -->
<div id="create-item-modal" class="modal-wrapper fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 modal-backdrop" onclick="closeModal('create-item-modal')"></div>
    <div class="relative glass-card modal-content rounded-2xl border border-slate-700 p-6 max-w-lg w-full shadow-2xl z-10 max-h-[90vh] overflow-y-auto">
        <h3 class="font-serif-dream text-lg font-bold text-white mb-1">Tambah Unit Prop Baru</h3>
        <p class="text-xs text-slate-400 mb-4">Daftarkan artefak fisik surreal ke dalam katalog.</p>

        <form method="POST" action="{{ route('admin.items.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 px-4 py-3">
                <p class="text-xs font-semibold text-amber-300">Kode unit dibuat otomatis</p>
                <p class="mt-1 text-xs text-slate-400">Kode dibentuk dari huruf awal setiap kata pada nama barang, diikuti ID database. Contoh: Chronos Sandglass menjadi CS-123.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Prop *</label>
                <input type="text" name="name" required
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none placeholder-slate-500"
                       placeholder="Misal: The Yesterday Watch">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Kategori</label>
                <div class="p-3 rounded-xl bg-slate-900 border border-slate-700 max-h-36 overflow-y-auto space-y-2">
                    @foreach ($categories as $cat)
                        <label class="flex items-center gap-2 text-xs text-slate-300 hover:text-white cursor-pointer">
                            <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}"
                                   class="rounded bg-slate-800 border-slate-600 text-amber-500 focus:ring-amber-500/20">
                            <span>{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi & Efek Surreal</label>
                <textarea name="description" rows="3"
                          class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none placeholder-slate-500"
                          placeholder="Penjelasan rupa prop fisik dan sifat fantasi surreal..."></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Foto Prop (JPG, PNG, WEBP; maks. 2 MB)</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-800 file:px-3 file:py-2 file:text-amber-300">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="closeModal('create-item-modal')" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-lg glow-gold">
                    Simpan Unit Prop
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Item -->
<div id="edit-item-modal" class="modal-wrapper fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 modal-backdrop" onclick="closeModal('edit-item-modal')"></div>
    <div class="relative glass-card modal-content rounded-2xl border border-slate-700 p-6 max-w-lg w-full shadow-2xl z-10 max-h-[90vh] overflow-y-auto">
        <h3 class="font-serif-dream text-lg font-bold text-white mb-1">Edit Unit Prop</h3>
        <p class="text-xs text-slate-400 mb-4">Perbarui informasi barang props.</p>

        <form id="edit-item-form" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Kode Unit (otomatis, tidak dapat diubah)</label>
                <input type="text" id="edit-item-unit-code" readonly
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-amber-300 text-sm font-mono cursor-not-allowed">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Prop *</label>
                <input type="text" id="edit-item-name" name="name" required
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Kategori</label>
                <div class="p-3 rounded-xl bg-slate-900 border border-slate-700 max-h-36 overflow-y-auto space-y-2">
                    @foreach ($categories as $cat)
                        <label class="flex items-center gap-2 text-xs text-slate-300 hover:text-white cursor-pointer">
                            <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" id="edit-cat-{{ $cat->id }}"
                                   class="edit-item-cat-checkbox rounded bg-slate-800 border-slate-600 text-amber-500 focus:ring-amber-500/20">
                            <span>{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi</label>
                <textarea id="edit-item-description" name="description" rows="3"
                          class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Foto Prop (JPG, PNG, WEBP; maks. 2 MB)</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-800 file:px-3 file:py-2 file:text-amber-300">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="closeModal('edit-item-modal')" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-lg glow-gold">
                    Perbarui Prop
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Archive Item -->
<div id="archive-item-modal" class="modal-wrapper fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 modal-backdrop" onclick="closeModal('archive-item-modal')"></div>
    <div class="relative glass-card modal-content rounded-2xl border border-rose-500/30 p-6 max-w-md w-full shadow-2xl z-10">
        <div class="flex items-center gap-3 text-rose-400 mb-3">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="font-serif-dream text-base font-bold text-white">Konfirmasi Pengarsipan Prop</h3>
        </div>
        <p class="text-xs text-slate-300 mb-4 leading-relaxed">
            Apakah Anda yakin ingin mengarsipkan unit prop <strong id="archive-item-name" class="text-amber-300"></strong> (<span id="archive-item-code" class="font-mono text-amber-400"></span>)?
            Unit yang diarsipkan disembunyikan dari katalog Member namun riwayat pinjamannya tetap terjaga di sistem.
        </p>
        <form id="archive-item-form" method="POST" class="flex items-center justify-end gap-3">
            @csrf
            @method('PATCH')
            <button type="button" onclick="closeModal('archive-item-modal')" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 transition">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition shadow-lg">
                Ya, Arsipkan Prop
            </button>
        </form>
    </div>
</div>

<script>
    function openEditItem(item, categoryIds) {
        document.getElementById('edit-item-form').action = '/admin/items/' + item.id;
        document.getElementById('edit-item-unit-code').value = item.unit_code || '';
        document.getElementById('edit-item-name').value = item.name || '';
        document.getElementById('edit-item-description').value = item.description || '';

        // Reset and check assigned categories
        document.querySelectorAll('.edit-item-cat-checkbox').forEach(cb => cb.checked = false);
        if (Array.isArray(categoryIds)) {
            categoryIds.forEach(id => {
                const el = document.getElementById('edit-cat-' + id);
                if (el) el.checked = true;
            });
        }

        openModal('edit-item-modal');
    }

    function openArchiveItem(id, name, unitCode) {
        document.getElementById('archive-item-form').action = '/admin/items/' + id + '/archive';
        document.getElementById('archive-item-name').textContent = name;
        document.getElementById('archive-item-code').textContent = unitCode;
        openModal('archive-item-modal');
    }
</script>
@endsection
