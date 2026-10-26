@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#07090e] flex flex-col">
    <!-- Member Header / Navbar -->
    <header class="border-b border-slate-800/80 bg-[#0a0f1d]/90 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <!-- Brand -->
            <div class="flex items-center gap-8">
                <a href="{{ route('member.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-300 flex items-center justify-center text-slate-950 font-bold shadow-lg glow-gold group-hover:scale-105 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-serif-dream font-bold text-lg text-amber-200 tracking-wider">Dream Pawn Shop</span>
                        <span class="block text-[11px] text-amber-500/80 font-medium tracking-widest uppercase">Member Portal</span>
                    </div>
                </a>

                <!-- Nav links -->
                <nav class="hidden md:flex items-center gap-1">
                    @php
                        $memberLinks = [
                            ['route' => 'member.dashboard', 'label' => 'Dashboard'],
                            ['route' => 'member.catalog.items.index', 'label' => 'Katalog Props'],
                            ['route' => 'member.loan-requests.index', 'label' => 'Pinjaman Saya'],
                            ['route' => 'member.fines.index', 'label' => 'Denda Saya'],
                            ['route' => 'member.profile.show', 'label' => 'Profil'],
                        ];
                    @endphp

                    @foreach ($memberLinks as $lnk)
                        @php
                            $isActive = request()->routeIs($lnk['route'].'*');
                        @endphp
                        <a href="{{ route($lnk['route']) }}" 
                           class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $isActive ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                            {{ $lnk['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <!-- Right: Allocation chip, User Info & Logout -->
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900 border border-slate-800 text-xs">
                    <span class="text-slate-400">Aturan:</span>
                    <span class="text-amber-400 font-semibold">Maks 2 Unit</span>
                    <span class="text-slate-600">|</span>
                    <span class="text-emerald-400 font-semibold">5 Hari Gratis</span>
                </div>

                <div class="h-6 w-px bg-slate-800 hidden sm:block"></div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('member.profile.show') }}" class="flex items-center gap-2.5 text-left group">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-600/30 to-amber-400/20 border border-amber-500/40 flex items-center justify-center text-xs font-bold text-amber-300 group-hover:scale-105 transition">
                            {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 1)) }}
                        </div>
                        <div class="hidden lg:block">
                            <p class="text-xs font-semibold text-slate-200 group-hover:text-amber-300 transition">{{ auth()->user()->name ?? 'Member' }}</p>
                            <p class="text-[11px] text-slate-400">{{ auth()->user()->email ?? '' }}</p>
                        </div>
                    </a>

                    <form method="POST" action="{{ route('member.logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 border border-slate-800 hover:border-rose-500/30 rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-6 py-8">
        @include('components.flash')
        @yield('member_content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-900 bg-[#06080e] py-8 text-center text-xs text-slate-400">
        <p class="font-serif-dream text-slate-400 mb-1">Dream Pawn Shop — Rental House of Surreal Collectible Props</p>
        <p>Bukan toko gadai sungguhan. Seluruh koleksi adalah properti fisik koleksi fantasi.</p>
    </footer>
</div>
@endsection
