@extends('layouts.app')

@section('title', 'Dream Pawn Shop — Rental House of Surreal Collectible Props')

@section('content')
<!-- Public Navigation Bar -->
<header class="border-b border-slate-800/80 bg-[#07090e]/80 backdrop-blur-md sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <!-- Brand -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 via-amber-400 to-amber-200 flex items-center justify-center text-slate-950 font-bold shadow-lg glow-gold group-hover:scale-105 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="font-serif-dream font-bold text-lg text-white tracking-wider group-hover:text-amber-200 transition">Dream Pawn Shop</span>
                <span class="block text-[10px] text-amber-400/80 font-medium tracking-widest uppercase">Surreal Props Sanctuary</span>
            </div>
        </a>

        <!-- Middle Links -->
        <nav class="hidden md:flex items-center gap-6 text-xs font-medium text-slate-400">
            <a href="#tentang" class="hover:text-amber-300 transition">Tentang Kami</a>
            <a href="#aturan" class="hover:text-amber-300 transition">Aturan Rental</a>
            <a href="#katalog" class="hover:text-amber-300 transition">Koleksi Props</a>
        </nav>

        <!-- Right Quick Portal Buttons -->
        <div class="flex items-center gap-3">
            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-semibold hover:bg-amber-500/30 transition flex items-center gap-1.5 shadow glow-gold">
                        <span>Command Center Admin</span> &rarr;
                    </a>
                @else
                    <a href="{{ route('member.dashboard') }}" class="px-4 py-2 rounded-xl bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-semibold hover:bg-cyan-500/30 transition flex items-center gap-1.5 shadow glow-cyan">
                        <span>Dashboard Member</span> &rarr;
                    </a>
                @endif
            @else
                <a href="{{ route('member.login.view') }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-700 text-xs font-medium transition">
                    Masuk Member
                </a>
                <a href="{{ route('admin.login.view') }}" class="px-3.5 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-semibold transition flex items-center gap-1">
                    <span>Admin</span> &rarr;
                </a>
            @endauth
        </div>
    </div>
</header>

<!-- Hero Section -->
<section class="relative pt-16 pb-20 md:pt-24 md:pb-32 overflow-hidden">
    <div class="max-w-5xl mx-auto px-6 text-center space-y-6 relative z-10">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-xs font-medium text-amber-300 shadow-lg glow-gold">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
            <span>Bukan Toko Gadai Sungguhan • Rental Props Fisik Koleksi</span>
        </div>

        <!-- Main Title -->
        <h1 class="font-serif-dream text-4xl sm:text-5xl md:text-6xl font-extrabold text-white tracking-wide leading-tight">
            Pinjam Artefak & Props Fisik Dari <span class="bg-gradient-to-r from-amber-400 via-amber-300 to-amber-500 bg-clip-text text-transparent">Alam Surreal</span>
        </h1>

        <!-- Subtitle -->
        <p class="text-sm md:text-base text-slate-400 max-w-2xl mx-auto leading-relaxed">
            Dream Pawn Shop adalah rumah sewa untuk koleksi properti fisik bernuansa magis dan fantasi. Seluruh peminjaman gratis selama 5 hari untuk maksimal 2 unit props.
        </p>

        <!-- CTA Buttons -->
        <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('member.catalog.items.index') }}" 
               class="px-6 py-3.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm transition transform hover:-translate-y-0.5 shadow-xl glow-gold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Jelajahi Katalog Props</span>
            </a>

            <a href="{{ route('member.login.view') }}" 
               class="px-6 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-700 font-semibold text-sm transition flex items-center gap-2">
                <span>Login Sebagai Member</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <!-- Quick Local Testing Bar -->
        <div class="mt-8 pt-6 border-t border-slate-800/80 max-w-xl mx-auto">
            <p class="text-xs text-slate-500 mb-3">Akses Cepat Pengujian Lokal:</p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('member.login.view') }}" class="px-3.5 py-1.5 rounded-lg bg-cyan-950/40 hover:bg-cyan-900/60 text-cyan-300 border border-cyan-500/30 text-xs transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                    <span>Demo Member: member@dreampawn.test</span>
                </a>
                <a href="{{ route('admin.login.view') }}" class="px-3.5 py-1.5 rounded-lg bg-amber-950/40 hover:bg-amber-900/60 text-amber-300 border border-amber-500/30 text-xs transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span>Demo Admin: admin@dreampawn.test</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Three Core Business Rules (Pillars) -->
<section id="aturan" class="py-16 border-t border-slate-800/80 bg-[#090d16]/60">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center max-w-xl mx-auto mb-12">
            <h2 class="font-serif-dream text-2xl font-bold text-white tracking-wide">3 Aturan Baku Dream Pawn Shop</h2>
            <p class="text-xs text-slate-400 mt-2">Sistem rental props fisik yang tertib, aman, dan transparan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Rule 1 -->
            <div class="glass-card rounded-2xl p-6 border border-slate-800 hover:border-amber-500/30 transition">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center font-bold text-lg font-mono mb-4">
                    5D
                </div>
                <h3 class="font-serif-dream text-base font-bold text-white mb-2">Gratis 5 Hari Kalender</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Durasi rental gratis berlaku selama 5 hari kalender penuh terhitung sejak waktu serah-terima fisik aktual (<code class="text-amber-400">handed_over_at</code>), bukan saat pengajuan dibuat.
                </p>
            </div>

            <!-- Rule 2 -->
            <div class="glass-card rounded-2xl p-6 border border-slate-800 hover:border-cyan-500/30 transition">
                <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-bold text-lg font-mono mb-4">
                    2U
                </div>
                <h3 class="font-serif-dream text-base font-bold text-white mb-2">Batas Kuota 2 Unit Props</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Setiap Member dapat memegang maksimal 2 unit fisik sekaligus. Pengajuan pending, reservasi approved, dan pinjaman aktif seluruhnya ikut dihitung dalam batas kuota.
                </p>
            </div>

            <!-- Rule 3 -->
            <div class="glass-card rounded-2xl p-6 border border-slate-800 hover:border-purple-500/30 transition">
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center justify-center font-bold text-lg font-mono mb-4">
                    RP
                </div>
                <h3 class="font-serif-dream text-base font-bold text-white mb-2">Denda Keterlambatan Rp 10.000</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Keterlambatan dihitung Rp 10.000 per 24 jam penuh per unit fisik. Keterlambatan di bawah 24 jam belum dikenakan denda. Member dengan denda tertunggak dilarang meminjam.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Props Showcase -->
<section id="katalog" class="py-16">
    <div class="max-w-6xl mx-auto px-6 space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-serif-dream text-2xl font-bold text-white tracking-wide">Koleksi Artefak Pilihan</h2>
                <p class="text-xs text-slate-400 mt-1">Contoh props fisik yang tersedia di rental house Dream Pawn Shop.</p>
            </div>
            <a href="{{ route('member.catalog.items.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-semibold flex items-center gap-1 self-start sm:self-auto">
                <span>Lihat Semua di Katalog</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($items as $item)
                <div class="glass-card rounded-2xl p-6 border border-slate-800 flex flex-col justify-between hover:border-amber-500/30 transition duration-300">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="font-mono text-xs font-bold text-amber-400 px-2 py-0.5 rounded bg-amber-500/10 border border-amber-500/20">
                                {{ $item->unit_code }}
                            </span>
                            @include('components.status-badge', ['status' => $item->status])
                        </div>

                        <h3 class="font-serif-dream text-lg font-bold text-white mb-2">{{ $item->name }}</h3>

                        <div class="flex flex-wrap gap-1.5 mb-3">
                            @foreach ($item->categories as $c)
                                <span class="px-2 py-0.5 rounded-md bg-slate-800 border border-slate-700/80 text-[10px] text-slate-300 font-medium">
                                    {{ $c->name }}
                                </span>
                            @endforeach
                        </div>

                        <p class="text-xs text-slate-400 leading-relaxed line-clamp-3 mb-4">
                            {{ $item->description ?? 'Artefak surreal tanpa deskripsi khusus.' }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Masa Sewa: 5 Hari</span>
                        <a href="{{ route('member.catalog.items.show', $item->id) }}" class="text-amber-400 hover:text-amber-300 font-semibold">
                            Detail & Pinjam &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    @include('components.empty-state', [
                        'title' => 'Katalog Sedang Diperbarui',
                        'description' => 'Silakan jalankan seeder katalog untuk mengisi daftar props.'
                    ])
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="border-t border-slate-900 bg-[#05070c] py-12 text-center text-xs text-slate-500 space-y-2">
    <p class="font-serif-dream text-slate-400 text-sm font-semibold tracking-wider">Dream Pawn Shop</p>
    <p>Rental house untuk collectible props fisik dengan konsep fantasi/surreal — bukan toko gadai sungguhan.</p>
    <p class="text-[11px] text-slate-600">Dokumentasi & Kontrak Arsitektur: <code class="text-slate-400 font-mono">docs/TEAM_HANDOFF_ADMIN_AND_FRONTEND.md</code></p>
</footer>
@endsection
