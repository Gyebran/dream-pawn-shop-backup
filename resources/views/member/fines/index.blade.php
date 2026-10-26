@extends('layouts.member')

@section('title', 'Denda Peminjaman Saya — Dream Pawn Shop')

@section('member_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif-dream text-2xl font-bold text-white tracking-wide">Denda Keterlambatan Saya</h1>
            <p class="text-xs text-slate-400 mt-1">
                Aturan Denda: <strong>Rp 10.000 / hari penuh keterlambatan / unit fisik</strong>.
            </p>
        </div>
    </div>

    <!-- Settlement Info Alert -->
    <div class="p-5 rounded-2xl glass-card border border-amber-500/30 flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="text-xs text-slate-300 space-y-1 leading-relaxed">
            <h4 class="font-bold text-amber-300 text-sm">Cara Pelunasan Denda</h4>
            <p>
                Pelunasan denda dilakukan langsung di konter fisik Dream Pawn Shop. Setelah pembayaran diterima, Admin akan mengonfirmasi status denda menjadi <strong>Lunas (Paid)</strong> dan Anda dapat mengajukan peminjaman props baru kembali.
            </p>
        </div>
    </div>

    <!-- Fines Table -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">ID Denda</th>
                        <th class="px-6 py-4">Barang Props</th>
                        <th class="px-6 py-4">Nominal Denda</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal Dinilai</th>
                        <th class="px-6 py-4">Waktu Pelunasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse ($fines as $fine)
                        @php
                            $detail = $fine->loanRequestItem;
                            $prop = $detail?->item;
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-mono font-bold text-amber-400">#{{ $fine->id }}</td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-200 block">{{ $prop->name ?? 'Unit #'.$fine->loan_request_item_id }}</span>
                                <span class="text-[11px] text-amber-400 font-mono">{{ $prop->unit_code ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-sm {{ $fine->status === 'unpaid' ? 'text-rose-400' : 'text-emerald-400' }}">
                                Rp {{ number_format($fine->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @include('components.status-badge', ['status' => $fine->status])
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $fine->assessed_at ? $fine->assessed_at->format('d M Y, H:i') : ($fine->created_at ? $fine->created_at->format('d M Y, H:i') : '-') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($fine->status === 'paid' && $fine->paid_confirmed_at)
                                    <span class="text-emerald-400 font-medium">{{ $fine->paid_confirmed_at->format('d M Y, H:i') }}</span>
                                @else
                                    <span class="text-rose-400 text-[11px]">Belum Lunas — Hubungi Admin</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                @include('components.empty-state', [
                                    'title' => 'Tidak Ada Catatan Denda',
                                    'description' => 'Akun Anda bersih tanpa ada catatan denda keterlambatan.'
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
@endsection
