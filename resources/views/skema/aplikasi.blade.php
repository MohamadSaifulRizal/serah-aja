<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    
    <link rel="icon" href="https://fav.farm/🍳" />
    <title>Buku Kas Dapur MBG</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
    
    @include('komponen.tailwind')

    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
</head>
<body class="font-sans text-ink antialiased h-screen overflow-hidden print:h-auto print:overflow-visible">
    <div class="flex h-screen w-full print:block print:h-auto" x-data="{ bukaNav: false }">

        @include('komponen.sidebar')

        <div x-show="bukaNav" 
             @click="bukaNav = false" 
             class="fixed inset-0 bg-black/40 z-40 md:hidden" 
             x-cloak>
        </div>
        
        <button @click="bukaNav = !bukaNav" 
                x-show="!bukaNav" 
                class="print:hidden md:hidden fixed top-4 left-4 z-50 flex items-center gap-1.5 bg-green-deep text-white px-3 py-2 rounded-md text-xs font-mono shadow-md transition-all duration-200"
                x-cloak>
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            Menu
        </button>

        <div class="flex-1 min-w-0 flex flex-col h-screen overflow-hidden print:h-auto print:overflow-visible print:block">
            <main class="flex-1 overflow-y-auto px-4 pt-20 pb-8 sm:px-6 md:px-10 md:py-8 print:overflow-visible print:h-auto print:p-0">
                @yield('konten')
            </main>
            @include('komponen.footer')
        </div>
    </div>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>