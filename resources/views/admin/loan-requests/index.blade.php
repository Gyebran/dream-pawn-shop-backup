@extends('layouts.admin')

@section('title', 'Loan Requests — Admin Dream Pawn Shop')
@section('page_title', 'Kelola Pengajuan & Pinjaman')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif-dream text-xl font-bold text-white tracking-wide">Pengajuan & Transaksi Peminjaman Props</h1>
            <p class="text-xs text-slate-400 mt-1">
                Alur: <strong>Pending</strong> (Review & Approval) &rarr; <strong>Approved</strong> (Tenggat Serah Terima 24 Jam) &rarr; <strong>Active</strong> (5 Hari Peminjaman) &rarr; <strong>Returned</strong> (Semua unit dikembalikan).
            </p>
        </div>
    </div>

    <!-- Filter Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-800">
        @php
            $currentStatus = $filters['status'] ?? '';
            $statusTabs = [
                '' => 'Semua Request',
                'pending' => 'Pending (Menunggu Review)',
                'approved' => 'Approved (Menunggu Serah Terima)',
                'active' => 'Active (Sedang Dipinjam)',
                'returned' => 'Returned (Selesai)',
                'rejected' => 'Rejected (Ditolak)',
                'cancelled' => 'Cancelled (Expired)',
            ];
        @endphp

        @foreach ($statusTabs as $val => $label)
            @php
                $tabParams = request()->except(['page', 'status']);
                if ($val !== '') {
                    $tabParams['status'] = $val;
                }
            @endphp
            <a href="{{ route('admin.loan-requests.index', $tabParams) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition {{ $currentStatus === $val ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Loans Table -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Member</th>
                        <th class="px-6 py-4">Props Yang Diajukan</th>
                        <th class="px-6 py-4">Status & Timeline</th>
                        <th class="px-6 py-4">Keterangan Waktu</th>
                        <th class="px-6 py-4 text-right">Aksi Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse ($loans as $loan)
                        @php
                            $now = now();
                            $isOverdue = ($loan->status === 'active' && $loan->due_at && $now->gt($loan->due_at));
                            $overdueSecs = $isOverdue ? $now->getTimestamp() - $loan->due_at->getTimestamp() : 0;
                            $overdueDays = intdiv($overdueSecs, 86400);
                            $unitCount = $loan->details->count();
                            $estimatedFine = $overdueDays * 10000 * $unitCount;
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition {{ $isOverdue ? 'bg-rose-950/20' : '' }}">
                            <td class="px-6 py-4 font-mono font-bold text-amber-400">
                                #{{ $loan->id }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-100 block">{{ $loan->member->name ?? 'Member #'.$loan->member_id }}</span>
                                <span class="text-[11px] text-slate-500 font-mono">{{ $loan->member->email ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @foreach ($loan->details as $d)
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-mono text-amber-400 font-medium text-[11px]">[{{ $d->item->unit_code ?? '-' }}]</span>
                                            <span class="text-slate-200">{{ $d->item->name ?? 'Item #'.$d->item_id }}</span>
                                        </div>
                                    @endforeach
                                    <span class="text-[10px] text-slate-500 font-mono">Total: {{ $unitCount }} unit fisik</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @include('components.status-badge', ['status' => $loan->status])
                                @if ($isOverdue)
                                    <span class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        TERLAMBAT {{ $overdueDays }} Hari (Denda Rp {{ number_format($estimatedFine, 0, ',', '.') }})
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-[11px] space-y-0.5">
                                <div>Diajukan: {{ $loan->requested_at ? $loan->requested_at->format('d/m/Y H:i') : '-' }}</div>
                                @if ($loan->status === 'approved' && $loan->handover_deadline_at)
                                    <div class="text-amber-400 font-medium">Batas Handover: {{ $loan->handover_deadline_at->format('d/m/Y H:i') }}</div>
                                @elseif ($loan->status === 'active')
                                    <div>Serah Terima: {{ $loan->handed_over_at ? $loan->handed_over_at->format('d/m/Y H:i') : '-' }}</div>
                                    <div class="{{ $isOverdue ? 'text-rose-400 font-bold' : 'text-emerald-400' }}">
                                        Jatuh Tempo: {{ $loan->due_at ? $loan->due_at->format('d/m/Y H:i') : '-' }}
                                    </div>
                                @elseif ($loan->status === 'returned')
                                    <div class="text-purple-400">Kembali: {{ $loan->returned_at ? $loan->returned_at->format('d/m/Y H:i') : '-' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-1.5">
                                @if ($loan->status === 'pending')
                                    <button type="button" onclick="openActionModal('approve', {{ $loan->id }}, '{{ addslashes($loan->member->name ?? '') }}', {{ $unitCount }})"
                                            class="px-2.5 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs transition shadow glow-cyan">
                                        Approve
                                    </button>
                                    <button type="button" onclick="openActionModal('reject', {{ $loan->id }}, '{{ addslashes($loan->member->name ?? '') }}', {{ $unitCount }})"
                                            class="px-2.5 py-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 font-medium text-xs transition">
                                        Reject
                                    </button>
                                @elseif ($loan->status === 'approved')
                                    <button type="button" onclick="openActionModal('handover', {{ $loan->id }}, '{{ addslashes($loan->member->name ?? '') }}', {{ $unitCount }})"
                                            class="px-2.5 py-1.5 rounded-lg bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-xs transition shadow">
                                        Serahkan (Handover)
                                    </button>
                                @elseif ($loan->status === 'active')
                                    <button type="button" onclick="openActionModal('confirm-return', {{ $loan->id }}, '{{ addslashes($loan->member->name ?? '') }}', {{ $unitCount }}, {{ $estimatedFine }})"
                                            class="px-2.5 py-1.5 rounded-lg bg-purple-500 hover:bg-purple-400 text-slate-950 font-bold text-xs transition shadow">
                                        Konfirmasi Pengembalian
                                    </button>
                                @else
                                    <span class="text-slate-600 text-[11px] italic">Selesai (Read-Only)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                @include('components.empty-state', [
                                    'title' => 'Tidak Ada Pengajuan',
                                    'description' => 'Belum ada loan request yang cocok dengan filter status.'
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $loans])
    </div>
</div>

<!-- Dynamic Action Modal (Approve / Reject / Handover / Confirm Return) -->
<div id="loan-action-modal" class="modal-wrapper fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 modal-backdrop" onclick="closeModal('loan-action-modal')"></div>
    <div class="relative glass-card modal-content rounded-2xl border border-slate-700 p-6 max-w-md w-full shadow-2xl z-10">
        <h3 id="modal-action-title" class="font-serif-dream text-lg font-bold text-white mb-2"></h3>
        <p id="modal-action-desc" class="text-xs text-slate-300 mb-4 leading-relaxed"></p>

        <form id="loan-action-form" method="POST" class="flex items-center justify-end gap-3 pt-2">
            @csrf
            <button type="button" onclick="closeModal('loan-action-modal')" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 transition">
                Batal
            </button>
            <button type="submit" id="modal-action-btn" class="px-4 py-2 rounded-xl font-bold text-xs transition shadow-lg">
                Konfirmasi
            </button>
        </form>
    </div>
</div>

<script>
    function openActionModal(action, loanId, memberName, unitCount, fineAmount = 0) {
        const form = document.getElementById('loan-action-form');
        const title = document.getElementById('modal-action-title');
        const desc = document.getElementById('modal-action-desc');
        const btn = document.getElementById('modal-action-btn');

        if (action === 'approve') {
            form.action = '/admin/loan-requests/' + loanId + '/approve';
            title.textContent = 'Persetujuan Pengajuan (Approve)';
            desc.innerHTML = `Setujui pengajuan loan #${loanId} untuk Member <strong class="text-amber-300">${memberName}</strong>? Seluruh ${unitCount} unit fisik akan <strong>direservasi secara atomik</strong>. Member memiliki waktu 24 jam untuk serah-terima fisik sebelum reservasi hangus.`;
            btn.className = 'px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs transition shadow-lg glow-cyan';
            btn.textContent = 'Ya, Setujui & Reservasi Unit';
        } else if (action === 'reject') {
            form.action = '/admin/loan-requests/' + loanId + '/reject';
            title.textContent = 'Tolak Pengajuan (Reject)';
            desc.innerHTML = `Tolak pengajuan loan #${loanId} dari Member <strong class="text-amber-300">${memberName}</strong>? Pengajuan akan berstatus ditolak dan kuota pending member dilepaskan.`;
            btn.className = 'px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition shadow-lg';
            btn.textContent = 'Tolak Pengajuan';
        } else if (action === 'handover') {
            form.action = '/admin/loan-requests/' + loanId + '/handover';
            title.textContent = 'Serah Terima Fisik (Handover)';
            desc.innerHTML = `Konfirmasi penyerahan fisik ${unitCount} unit prop kepada Member <strong class="text-amber-300">${memberName}</strong>? Tindakan ini akan mengaktifkan status pinjaman dan <strong>memulai masa rental gratis 5 hari</strong> terhitung sejak waktu serah terima ini.`;
            btn.className = 'px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-xs transition shadow-lg';
            btn.textContent = 'Konfirmasi Serah Terima';
        } else if (action === 'confirm-return') {
            form.action = '/admin/loan-requests/' + loanId + '/confirm-return';
            title.textContent = 'Konfirmasi Pengembalian Props';
            let extra = fineAmount > 0 
                ? `<div class="mt-2 p-2.5 rounded-lg bg-rose-500/20 text-rose-300 border border-rose-500/30"><strong>Perhatian:</strong> Peminjaman ini terlambat. Sistem akan mencatat denda sebesar <strong>Rp ${fineAmount.toLocaleString('id-ID')}</strong> (Rp 10.000 / hari keterlambatan / unit).</div>` 
                : `<div class="mt-2 text-emerald-400">Peminjaman kembali tepat waktu tanpa denda.</div>`;
            desc.innerHTML = `Konfirmasi bahwa seluruh ${unitCount} unit fisik props telah dikembalikan oleh Member <strong class="text-amber-300">${memberName}</strong> dalam keadaan baik? Seluruh unit akan kembali berstatus <strong>available</strong>.${extra}`;
            btn.className = 'px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs transition shadow-lg';
            btn.textContent = 'Konfirmasi Pengembalian Lengkap';
        }

        openModal('loan-action-modal');
    }
</script>
@endsection
