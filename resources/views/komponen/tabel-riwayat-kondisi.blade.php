{{--
    Tabel riwayat update kondisi barang inventaris + biayanya.
    Dipakai di Management Dapur (detail dapur) dan Dashboard Admin Dapur.
    $riwayat = Collection<RiwayatKondisiInventaris> (relasi barangInventaris.masterBarang & pengguna sudah di-load)
--}}
@include('komponen.hint-geser')
<div class="bg-card border border-paperline rounded-lg overflow-x-auto">
    <table class="w-full min-w-200 text-sm text-left">
        <thead>
            <tr class="bg-paper font-mono text-xs uppercase border-b border-paperline text-muted">
                <th class="p-3">Tanggal</th><th class="p-3">Barang</th><th class="p-3">Perubahan Kondisi</th><th class="p-3">Biaya</th><th class="p-3">Diupdate Oleh</th><th class="p-3">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $r)
            <tr class="border-b border-paperline">
                <td class="p-3 text-xs">{{ $r->tanggal_update->format('d-m-Y') }}</td>
                <td class="p-3 font-semibold">{{ $r->barangInventaris->namaBarang() }}</td>
                <td class="p-3 text-xs">{{ \App\Models\BarangInventaris::labelKondisi($r->kondisi_sebelum) }} &rarr; <b>{{ \App\Models\BarangInventaris::labelKondisi($r->kondisi_baru) }}</b></td>
                <td class="p-3 font-mono text-xs {{ $r->biaya > 0 ? 'text-rust font-bold' : 'text-muted' }}">{{ \App\Helpers\KalkulatorDapur::formatRupiah($r->biaya) }}</td>
                <td class="p-3 text-xs">{{ $r->namaPengguna() }}</td>
                <td class="p-3 text-xs text-muted">{{ $r->catatan ?: '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="p-4 text-center text-xs text-muted">Belum ada update kondisi barang inventaris.</td></tr>
            @endforelse
        </tbody>
        @if($riwayat->isNotEmpty())
        <tfoot>
            <tr class="bg-paper/60 font-bold">
                <td colspan="3" class="p-3 text-right text-xs uppercase text-muted">Total Biaya Update Kondisi</td>
                <td class="p-3 font-mono text-sm text-rust">{{ \App\Helpers\KalkulatorDapur::formatRupiah($riwayat->sum('biaya')) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
