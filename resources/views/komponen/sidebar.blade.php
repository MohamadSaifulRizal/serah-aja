@php($u = auth()->user())
<aside :class="bukaNav ? 'translate-x-0' : '-translate-x-full'" class="print:hidden fixed md:sticky top-0 inset-y-0 left-0 z-50 w-64 h-screen shrink-0 bg-green-deep text-[#EFEAD8] flex flex-col p-4 border-r-3 border-double border-[#16281a] transform transition-transform duration-200 md:translate-x-0">

    <div class="flex items-center justify-between pb-5 border-b border-white/15 mb-3.5">
      <div class="flex items-center gap-2.5">
          <div class="size-9 rounded-md bg-mustard text-[#2b1c04] flex items-center justify-center font-display font-bold text-[13px] -rotate-3 shrink-0">MBG</div>
          <div>
            <div class="font-display font-semibold text-[15px] leading-tight">Buku Kas Dapur</div>
            <div class="text-[11px] opacity-75">{{ $u?->name }} &middot; {{ $u?->labelRole() }}</div>
          </div>
      </div>
      <button @click="bukaNav = false" class="md:hidden text-white/80 hover:text-white">
          <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
      </button>
    </div>

    <nav class="flex flex-col gap-0.5 flex-1 overflow-y-auto">
        @php($adminDapur = $u?->isAdminDapur())

        {{-- Dashboard: Admin Dapur = diagram dapurnya sendiri, Admin Logistik = ringkasan semua dapur --}}
        <a href="{{ $adminDapur ? route('dapur.beranda') : route('dashboard') }}" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-md text-sm {{ Route::is('dapur.beranda') || Route::is('dashboard') ? 'bg-mustard text-ink font-semibold' : 'hover:bg-white/10' }}">Dashboard</a>

        <a href="{{ route('kategoriBahanPokok.indeks') }}" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-md text-sm {{ Route::is('kategoriBahanPokok.*') ? 'bg-mustard text-ink font-semibold' : 'hover:bg-white/10' }}">Kategori Bahan Pokok</a>

        <a href="{{ route('kategoriInventaris.indeks') }}" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-md text-sm {{ Route::is('kategoriInventaris.*') ? 'bg-mustard text-ink font-semibold' : 'hover:bg-white/10' }}">Kategori Barang Inventaris</a>

        @unless($adminDapur)
            {{-- Menu khusus Admin Logistik (Super Admin juga mendapat semua menu ini + Manajemen Akun) --}}
            <a href="{{ route('supplier.indeks') }}" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-md text-sm {{ Route::is('supplier.*') ? 'bg-mustard text-ink font-semibold' : 'hover:bg-white/10' }}">Supplier</a>

            <a href="{{ route('masterBarang.indeks') }}" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-md text-sm {{ Route::is('masterBarang.*') ? 'bg-mustard text-ink font-semibold' : 'hover:bg-white/10' }}">Master Barang</a>

        @endunless

        <a href="{{ route('bahanPokok.indeks') }}" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-md text-sm {{ Route::is('bahanPokok.*') || Route::is('dapur.formBeliMandiri') ? 'bg-mustard text-ink font-semibold' : 'hover:bg-white/10' }}">Bahan Pokok</a>

        <a href="{{ route('inventaris.indeks') }}" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-md text-sm {{ Route::is('inventaris.*') ? 'bg-mustard text-ink font-semibold' : 'hover:bg-white/10' }}">Barang Inventaris</a>

        @unless($adminDapur)
            <a href="{{ route('dapur.indeks') }}" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-md text-sm {{ Route::is('dapur.indeks') || Route::is('dapur.detail') ? 'bg-mustard text-ink font-semibold' : 'hover:bg-white/10' }}">Dapur</a>

            @if($u?->isSuperAdmin())
                <a href="{{ route('akun.indeks') }}" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-md text-sm {{ Route::is('akun.*') ? 'bg-mustard text-ink font-semibold' : 'hover:bg-white/10' }}">Manajemen Akun</a>
            @endif
        @endunless
    </nav>

    <div class="mt-4 pt-4 border-t border-white/15">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center justify-between px-3 py-2.5 rounded-md text-sm text-[#F3DBD3] hover:bg-rust hover:text-white transition-colors font-semibold group cursor-pointer">
                <span>Logout</span>
                <svg class="size-4 opacity-70 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </button>
        </form>
    </div>

</aside>
