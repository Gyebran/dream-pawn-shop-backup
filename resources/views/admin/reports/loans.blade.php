@extends('layouts.admin')

@section('title', 'Laporan Pinjaman — Admin Dream Pawn Shop')
@section('page_title', 'Laporan Transaksi Pinjaman')

@section('admin_content')
<div class="space-y-6">
    <!-- Header with Export CSV and PDF print -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif-dream text-xl font-bold text-white tracking-wide">Laporan Pinjaman Props</h1>
            <p class="text-xs text-slate-400 mt-1">
                Filter transaksi peminjaman berdasarkan rentang tanggal dan status. Hasil filter dapat diekspor langsung dalam format CSV.
            </p>
        </div>

        @php
            $exportUrl = route('admin.reports.loans.export', request()->query());
        @endphp
        <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.reports.loans.print', request()->query()) }}" target="_blank" rel="noopener" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow flex items-center justify-center gap-2 self-start sm:self-auto"><span>Cetak / Simpan PDF</span></a>
        <a href="{{ $exportUrl }}" 
           class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-300 font-bold text-xs border border-emerald-500/30 transition shadow flex items-center justify-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Export Laporan ke CSV</span>
        </a>
        </div>
    </div>

    <!-- Date Range & Filter Form -->
    <form method="GET" action="{{ route('admin.reports.loans.index') }}" class="glass-card rounded-2xl p-5 border border-slate-800 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Dari Tanggal (From)</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                       class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:border-amber-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal (To)</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                       class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:border-amber-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Status Pinjaman</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:border-amber-500 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="returned" {{ ($filters['status'] ?? '') === 'returned' ? 'selected' : '' }}>Returned</option>
                    <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow glow-gold">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.reports.loans.index') }}" class="py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs border border-slate-700 transition">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Report Table -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">ID Request</th>
                        <th class="px-6 py-4">Member</th>
                        <th class="px-6 py-4">Daftar Unit Props</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Waktu Pengajuan</th>
                        <th class="px-6 py-4">Jatuh Tempo</th>
                        <th class="px-6 py-4">Waktu Pengembalian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse ($report as $row)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-mono font-bold text-amber-400">#{{ $row->id }}</td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-100 block">{{ $row->member->name ?? 'Member #'.$row->member_id }}</span>
                                <span class="text-[11px] text-slate-500 font-mono">{{ $row->member->email ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @foreach ($row->details as $d)
                                    <div class="text-[11px]">
                                        <span class="text-amber-400 font-mono">[{{ $d->item->unit_code ?? '-' }}]</span>
                                        <span class="text-slate-200">{{ $d->item->name ?? '-' }}</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
                                @include('components.status-badge', ['status' => $row->status])
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $row->requested_at ? $row->requested_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $row->due_at ? $row->due_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $row->returned_at ? $row->returned_at->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                @include('components.empty-state', [
                                    'title' => 'Laporan Kosong',
                                    'description' => 'Tidak ada data peminjaman dalam rentang tanggal atau status yang dipilih.'
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $report])
    </div>
</div>
@endsection
