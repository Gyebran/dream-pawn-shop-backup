@extends('layouts.admin')

@section('title', 'Daftar Denda (Fines) — Admin Dream Pawn Shop')
@section('page_title', 'Kelola Denda Keterlambatan')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif-dream text-xl font-bold text-white tracking-wide">Denda Keterlambatan Pengembalian</h1>
            <p class="text-xs text-slate-400 mt-1">
                Aturan Denda: <strong>Rp 10.000 / hari keterlambatan penuh (24 jam) / unit fisik</strong>. Pelunasan dicatat langsung oleh Admin di konter toko.
            </p>
        </div>
    </div>

    <!-- Filter Status Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-800 pb-2">
        @php
            $currentStatus = $filters['status'] ?? '';
        @endphp
        <a href="{{ route('admin.fines.index') }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-medium transition {{ $currentStatus === '' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
            Semua Denda
        </a>
        <a href="{{ route('admin.fines.index', ['status' => 'unpaid']) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-medium transition {{ $currentStatus === 'unpaid' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
            Belum Lunas (Unpaid)
        </a>
        <a href="{{ route('admin.fines.index', ['status' => 'paid']) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-medium transition {{ $currentStatus === 'paid' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
            Lunas (Paid)
        </a>
    </div>

    <!-- Fines Table -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">ID Denda</th>
                        <th class="px-6 py-4">Member Peminjam</th>
                        <th class="px-6 py-4">Props Terkait</th>
                        <th class="px-6 py-4">Nominal Denda</th>
                        <th class="px-6 py-4">Status & Waktu</th>
                        <th class="px-6 py-4 text-right">Aksi Konfirmasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse ($fines as $fine)
                        @php
                            $loanItem = $fine->loanRequestItem;
                            $loan = $loanItem?->loanRequest;
                            $member = $loan?->member;
                            $prop = $loanItem?->item;
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-mono font-bold text-amber-400">
                                #{{ $fine->id }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-100 block">{{ $member->name ?? 'Member #'.($loan?->member_id ?? '-') }}</span>
                                <span class="text-[11px] text-slate-500 font-mono">{{ $member->email ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-200">{{ $prop->name ?? 'Item #'.($loanItem?->item_id ?? '-') }}</div>
                                <span class="text-[11px] text-amber-400 font-mono">{{ $prop->unit_code ?? '' }}</span>
                                <span class="text-slate-500 text-[10px]"> (Loan #{{ $loan?->id ?? '-' }})</span>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-sm {{ $fine->status === 'unpaid' ? 'text-rose-400' : 'text-emerald-400' }}">
                                Rp {{ number_format($fine->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 space-y-1">
                                <div>@include('components.status-badge', ['status' => $fine->status])</div>
                                <div class="text-[11px] text-slate-500">
                                    Dinilai: {{ $fine->assessed_at ? $fine->assessed_at->format('d/m/Y H:i') : ($fine->created_at ? $fine->created_at->format('d/m/Y H:i') : '-') }}
                                </div>
                                @if ($fine->status === 'paid' && $fine->paid_confirmed_at)
                                    <div class="text-[11px] text-emerald-400">
                                        Lunas: {{ $fine->paid_confirmed_at->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($fine->status === 'unpaid')
                                    <button type="button" 
                                            onclick="openPaymentModal({{ $fine->id }}, '{{ number_format($fine->amount, 0, ',', '.') }}', '{{ addslashes($member->name ?? 'Member') }}')"
                                            class="px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs transition shadow glow-cyan">
                                        Konfirmasi Lunas
                                    </button>
                                @else
                                    <span class="text-[11px] text-emerald-400/80 italic flex items-center justify-end gap-1">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Telah Dilunasi
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                @include('components.empty-state', [
                                    'title' => 'Denda Tidak Ditemukan',
                                    'description' => 'Tidak ada catatan denda yang cocok dengan filter saat ini.'
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $fines])
    </div>
</div>

<!-- Modal Confirm Payment -->
<div id="confirm-payment-modal" class="modal-wrapper fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 modal-backdrop" onclick="closeModal('confirm-payment-modal')"></div>
    <div class="relative glass-card modal-content rounded-2xl border border-emerald-500/30 p-6 max-w-md w-full shadow-2xl z-10">
        <div class="flex items-center gap-3 text-emerald-400 mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="font-serif-dream text-base font-bold text-white">Konfirmasi Pelunasan Denda</h3>
        </div>
        <p class="text-xs text-slate-300 mb-4 leading-relaxed">
            Konfirmasi bahwa Member <strong id="pay-member-name" class="text-amber-300"></strong> telah menyelesaikan pembayaran denda sebesar <strong id="pay-amount" class="text-emerald-400 font-mono text-sm"></strong> di konter toko?
            Setelah lunas, blokir pengajuan pinjaman pada akun member akan dicabut secara otomatis.
        </p>
        <form id="confirm-payment-form" method="POST" class="flex items-center justify-end gap-3">
            @csrf
            <button type="button" onclick="closeModal('confirm-payment-modal')" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 transition">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs transition shadow-lg glow-cyan">
                Ya, Konfirmasi Lunas
            </button>
        </form>
    </div>
</div>

<script>
    function openPaymentModal(fineId, amount, memberName) {
        document.getElementById('confirm-payment-form').action = '/admin/fines/' + fineId + '/confirm-payment';
        document.getElementById('pay-member-name').textContent = memberName;
        document.getElementById('pay-amount').textContent = 'Rp ' + amount;
        openModal('confirm-payment-modal');
    }
</script>
@endsection
