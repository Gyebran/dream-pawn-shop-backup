@extends('layouts.admin')

@section('title', 'Kategori Props — Admin Dream Pawn Shop')
@section('page_title', 'Kelola Kategori')

@section('admin_content')
<div class="space-y-6">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif-dream text-xl font-bold text-white tracking-wide">Kategori Koleksi Props</h1>
            <p class="text-xs text-slate-400 mt-1">Klasifikasi artefak surreal (Relics, Timepieces, Keys & Locks, dsb.)</p>
        </div>
        <button type="button" onclick="openModal('create-category-modal')"
                class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm transition shadow-lg glow-gold flex items-center justify-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Kategori Baru</span>
        </button>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('admin.categories.index') }}" class="glass-card rounded-2xl p-4 border border-slate-800 flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[240px] relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama kategori..."
                   class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-900/80 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-amber-500 transition placeholder-slate-500">
        </div>

        <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
            <input type="checkbox" name="include_archived" value="1" {{ !empty($filters['include_archived']) ? 'checked' : '' }}
                   class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-amber-500/20">
            <span>Tampilkan Kategori Diarsipkan</span>
        </label>

        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 transition">
            Filter
        </button>
        @if (!empty($filters['search']) || !empty($filters['include_archived']))
            <a href="{{ route('admin.categories.index') }}" class="text-xs text-rose-400 hover:underline">Reset</a>
        @endif
    </form>

    <!-- Categories Table -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Jumlah Props</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse ($categories as $cat)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-mono text-slate-500">#{{ $cat->id }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-100">{{ $cat->name }}</td>
                            <td class="px-6 py-4 text-slate-400 max-w-xs truncate">{{ $cat->description ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if ($cat->is_archived)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                        Diarsipkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-mono text-slate-300">
                                {{ $cat->items_count ?? $cat->items()->count() }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button type="button" 
                                        onclick="openEditCategory({{ json_encode($cat) }})"
                                        class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-300 border border-slate-700 text-[11px] font-medium transition">
                                    Edit
                                </button>
                                @if (!$cat->is_archived)
                                    <button type="button" 
                                            onclick="openArchiveCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')"
                                            class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/20 text-[11px] font-medium transition">
                                        Arsipkan
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                @include('components.empty-state', [
                                    'title' => 'Kategori Tidak Ditemukan',
                                    'description' => 'Belum ada kategori yang cocok dengan filter Anda.'
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $categories])
    </div>
</div>

<!-- Modal Create Category -->
<div id="create-category-modal" class="modal-wrapper fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 modal-backdrop" onclick="closeModal('create-category-modal')"></div>
    <div class="relative glass-card modal-content rounded-2xl border border-slate-700 p-6 max-w-md w-full shadow-2xl z-10">
        <h3 class="font-serif-dream text-lg font-bold text-white mb-1">Tambah Kategori Baru</h3>
        <p class="text-xs text-slate-400 mb-4">Tambahkan klasifikasi kategori untuk artefak surreal.</p>

        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Kategori *</label>
                <input type="text" name="name" required
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none placeholder-slate-500"
                       placeholder="Misal: Music Boxes, Relics">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none placeholder-slate-500"
                          placeholder="Penjelasan singkat konsep surreal kategori ini..."></textarea>
            </div>
            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="closeModal('create-category-modal')" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-lg glow-gold">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Category -->
<div id="edit-category-modal" class="modal-wrapper fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 modal-backdrop" onclick="closeModal('edit-category-modal')"></div>
    <div class="relative glass-card modal-content rounded-2xl border border-slate-700 p-6 max-w-md w-full shadow-2xl z-10">
        <h3 class="font-serif-dream text-lg font-bold text-white mb-1">Edit Kategori</h3>
        <p class="text-xs text-slate-400 mb-4">Perbarui informasi kategori.</p>

        <form id="edit-category-form" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Kategori *</label>
                <input type="text" id="edit-cat-name" name="name" required
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi</label>
                <textarea id="edit-cat-description" name="description" rows="3"
                          class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none"></textarea>
            </div>
            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="closeModal('edit-category-modal')" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-lg glow-gold">
                    Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Archive Category -->
<div id="archive-category-modal" class="modal-wrapper fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 modal-backdrop" onclick="closeModal('archive-category-modal')"></div>
    <div class="relative glass-card modal-content rounded-2xl border border-rose-500/30 p-6 max-w-md w-full shadow-2xl z-10">
        <div class="flex items-center gap-3 text-rose-400 mb-3">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="font-serif-dream text-base font-bold text-white">Konfirmasi Pengarsipan</h3>
        </div>
        <p class="text-xs text-slate-300 mb-4 leading-relaxed">
            Apakah Anda yakin ingin mengarsipkan kategori <strong id="archive-cat-name" class="text-amber-300"></strong>? Kategori ini tidak akan dihapus permanen untuk menjaga integritas riwayat relasi props.
        </p>
        <form id="archive-category-form" method="POST" class="flex items-center justify-end gap-3">
            @csrf
            @method('PATCH')
            <button type="button" onclick="closeModal('archive-category-modal')" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 transition">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition shadow-lg">
                Ya, Arsipkan Kategori
            </button>
        </form>
    </div>
</div>

<script>
    function openEditCategory(category) {
        document.getElementById('edit-category-form').action = '/admin/categories/' + category.id;
        document.getElementById('edit-cat-name').value = category.name || '';
        document.getElementById('edit-cat-description').value = category.description || '';
        openModal('edit-category-modal');
    }

    function openArchiveCategory(id, name) {
        document.getElementById('archive-category-form').action = '/admin/categories/' + id + '/archive';
        document.getElementById('archive-cat-name').textContent = name;
        openModal('archive-category-modal');
    }
</script>
@endsection
