{{-- Pemakaian: @include('komponen.tombol-kembali', ['href' => route('xxx'), 'label' => 'Kembali ke ...']) --}}
<a href="{{ $href }}" class="inline-flex items-center px-4 py-2 text-xs font-bold text-ink bg-white border border-paperline rounded-lg hover:bg-paper hover:text-green-deep hover:border-green/30 transition-all mb-6 group shadow-sm">
    <svg class="size-4 mr-1.5 text-muted group-hover:text-green-deep group-hover:-translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    {{ $label }}
</a>
