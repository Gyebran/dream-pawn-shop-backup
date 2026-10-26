@if (session('success'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/30 text-emerald-200 flex items-start gap-3 shadow-lg glow-cyan" role="alert">
        <svg class="w-5 h-5 text-emerald-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1 text-sm leading-relaxed">
            <strong class="font-semibold text-emerald-100">Berhasil!</strong> {{ session('success') }}
        </div>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 p-4 rounded-xl bg-rose-950/60 border border-rose-500/30 text-rose-200 flex items-start gap-3 shadow-lg" role="alert">
        <svg class="w-5 h-5 text-rose-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1 text-sm leading-relaxed">
            <strong class="font-semibold text-rose-100">Perhatian:</strong> {{ session('error') }}
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-rose-950/70 border border-rose-500/30 text-rose-200 shadow-lg" role="alert">
        <div class="flex items-center gap-2 mb-2 font-semibold text-rose-100 text-sm">
            <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Terdapat beberapa kesalahan input:</span>
        </div>
        <ul class="list-disc list-inside text-sm space-y-1 text-rose-300/90 pl-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
