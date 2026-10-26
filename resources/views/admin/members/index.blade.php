@extends('layouts.admin')

@section('title', 'Kelola Member — Admin Dream Pawn Shop')
@section('page_title', 'Kelola Akun Member')

@section('admin_content')
<div class="space-y-6">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif-dream text-xl font-bold text-white tracking-wide">Daftar Akun Member</h1>
            <p class="text-xs text-slate-400 mt-1">Kelola data peminjam koleksi props surreal. Akun tidak dihapus permanen melainkan dinonaktifkan.</p>
        </div>
        <button type="button" onclick="openModal('create-member-modal')"
                class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm transition shadow-lg glow-gold flex items-center justify-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span>Daftarkan Member Baru</span>
        </button>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('admin.members.index') }}" class="glass-card rounded-2xl p-4 border border-slate-800 flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[240px] relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama atau email member..."
                   class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-900/80 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-amber-500 transition placeholder-slate-500">
        </div>

        <div class="w-44">
            <select name="is_active" class="w-full px-3 py-2 rounded-xl bg-slate-900/80 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-amber-500">
                <option value="">Semua Status Akun</option>
                <option value="1" {{ ($filters['is_active'] ?? '') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ ($filters['is_active'] ?? '') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 transition">
            Filter
        </button>
        @if (!empty($filters['search']) || isset($filters['is_active']))
            <a href="{{ route('admin.members.index') }}" class="text-xs text-rose-400 hover:underline">Reset</a>
        @endif
    </form>

    <!-- Members Table -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Email Terdaftar</th>
                        <th class="px-6 py-4">Status Akun</th>
                        <th class="px-6 py-4">Terdaftar Sejak</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse ($members as $member)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-mono text-slate-500">#{{ $member->id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-amber-300 text-[11px]">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-slate-100">{{ $member->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-400">{{ $member->email }}</td>
                            <td class="px-6 py-4">
                                @include('components.status-badge', ['status' => $member->is_active, 'type' => 'member'])
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $member->created_at ? $member->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" 
                                        onclick="openEditMember({{ json_encode($member) }})"
                                        class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-300 border border-slate-700 text-[11px] font-medium transition">
                                    Edit Profil / Status
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                @include('components.empty-state', [
                                    'title' => 'Member Tidak Ditemukan',
                                    'description' => 'Belum ada akun member yang sesuai dengan pencarian.'
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $members])
    </div>
</div>

<!-- Modal Create Member -->
<div id="create-member-modal" class="modal-wrapper fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 modal-backdrop" onclick="closeModal('create-member-modal')"></div>
    <div class="relative glass-card modal-content rounded-2xl border border-slate-700 p-6 max-w-md w-full shadow-2xl z-10 max-h-[90vh] overflow-y-auto">
        <h3 class="font-serif-dream text-lg font-bold text-white mb-1">Daftarkan Member Baru</h3>
        <p class="text-xs text-slate-400 mb-4">Buat akun login Member untuk peminjaman props.</p>

        <form method="POST" action="{{ route('admin.members.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none placeholder-slate-500"
                       placeholder="Misal: John Constantine">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Email * (Unik)</label>
                <input type="email" name="email" required
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none placeholder-slate-500"
                       placeholder="member@example.com">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Password * (Min. 8 Karakter)</label>
                <input type="password" name="password" required
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none"
                       placeholder="••••••••">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Konfirmasi Password *</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="create-is-active" value="1" checked
                       class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-amber-500/20">
                <label for="create-is-active" class="text-xs text-slate-300 cursor-pointer">Akun langsung berstatus Aktif</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="closeModal('create-member-modal')" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-lg glow-gold">
                    Daftarkan Member
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Member -->
<div id="edit-member-modal" class="modal-wrapper fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 modal-backdrop" onclick="closeModal('edit-member-modal')"></div>
    <div class="relative glass-card modal-content rounded-2xl border border-slate-700 p-6 max-w-md w-full shadow-2xl z-10">
        <h3 class="font-serif-dream text-lg font-bold text-white mb-1">Edit Akun Member</h3>
        <p class="text-xs text-slate-400 mb-4">Perbarui informasi nama, email, dan status aktif akun.</p>

        <form id="edit-member-form" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap *</label>
                <input type="text" id="edit-member-name" name="name" required
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Email *</label>
                <input type="email" id="edit-member-email" name="email" required
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Status Keaktifan Akun</label>
                <select id="edit-member-status" name="is_active" class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-sm focus:border-amber-500 focus:outline-none">
                    <option value="1">Aktif (Dapat Mengajukan Peminjaman)</option>
                    <option value="0">Nonaktif (Akses Diblokir)</option>
                </select>
                <p class="text-[11px] text-slate-500 mt-1">Akun nonaktif tidak dapat login maupun mengajukan pinjaman props baru.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="closeModal('edit-member-modal')" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-lg glow-gold">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditMember(member) {
        document.getElementById('edit-member-form').action = '/admin/members/' + member.id;
        document.getElementById('edit-member-name').value = member.name || '';
        document.getElementById('edit-member-email').value = member.email || '';
        document.getElementById('edit-member-status').value = member.is_active ? '1' : '0';
        openModal('edit-member-modal');
    }
</script>
@endsection
