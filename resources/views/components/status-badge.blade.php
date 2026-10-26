@props(['status', 'type' => 'item'])

@php
    $s = strtolower((string)$status);
    $configs = [
        // Items
        'available' => ['label' => 'Tersedia', 'class' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 ring-emerald-500/20'],
        'reserved'  => ['label' => 'Direservasi', 'class' => 'bg-amber-500/10 text-amber-400 border-amber-500/20 ring-amber-500/20'],
        'loaned'    => ['label' => 'Sedang Dipinjam', 'class' => 'bg-sky-500/10 text-sky-400 border-sky-500/20 ring-sky-500/20'],
        'archived'  => ['label' => 'Diarsipkan', 'class' => 'bg-slate-500/10 text-slate-400 border-slate-500/20 ring-slate-500/20'],

        // Loan Requests
        'pending'   => ['label' => 'Menunggu Review', 'class' => 'bg-amber-500/10 text-amber-400 border-amber-500/20 ring-amber-500/20 animate-pulse'],
        'approved'  => ['label' => 'Disetujui (Reservasi)', 'class' => 'bg-blue-500/10 text-blue-400 border-blue-500/20 ring-blue-500/20'],
        'active'    => ['label' => 'Aktif (5 Hari)', 'class' => 'bg-teal-500/10 text-teal-400 border-teal-500/20 ring-teal-500/20'],
        'returned'  => ['label' => 'Dikembalikan', 'class' => 'bg-purple-500/10 text-purple-400 border-purple-500/20 ring-purple-500/20'],
        'rejected'  => ['label' => 'Ditolak', 'class' => 'bg-rose-500/10 text-rose-400 border-rose-500/20 ring-rose-500/20'],
        'cancelled' => ['label' => 'Dibatalkan (Expired)', 'class' => 'bg-slate-500/10 text-slate-400 border-slate-500/20 ring-slate-500/20'],

        // Fines
        'unpaid'    => ['label' => 'Belum Lunas', 'class' => 'bg-rose-500/10 text-rose-400 border-rose-500/20 ring-rose-500/20'],
        'paid'      => ['label' => 'Lunas', 'class' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 ring-emerald-500/20'],

        // Member account
        'active_account'   => ['label' => 'Aktif', 'class' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 ring-emerald-500/20'],
        'inactive_account' => ['label' => 'Nonaktif', 'class' => 'bg-rose-500/10 text-rose-400 border-rose-500/20 ring-rose-500/20'],
    ];

    if ($type === 'member') {
        $key = $status ? 'active_account' : 'inactive_account';
    } else {
        $key = $s;
    }

    $badge = $configs[$key] ?? ['label' => ucfirst($s), 'class' => 'bg-slate-500/10 text-slate-400 border-slate-500/20'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border ' . $badge['class']]) }}>
    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
    <span>{{ $badge['label'] }}</span>
</span>
