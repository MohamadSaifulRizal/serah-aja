<?php

namespace Tests\Feature;

use App\Models\BahanPokok;
use App\Models\BarangInventaris;
use App\Models\Dapur;
use App\Models\KategoriBarang;
use App\Models\LaporanPemakaianBahan;
use App\Models\MasterBarang;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalAdminDapurTest extends TestCase
{
    use RefreshDatabase;

    private Dapur $a;
    private Dapur $b;
    private User $adminA;
    private User $logistik;
    private MasterBarang $mbInv;
    private MasterBarang $mbBahan;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->a = Dapur::create(['nama_dapur' => 'Dapur A', 'alamat' => 'Jl A', 'penanggung_jawab' => 'PJ A']);
        $this->b = Dapur::create(['nama_dapur' => 'Dapur B', 'alamat' => 'Jl B', 'penanggung_jawab' => 'PJ B']);
        $this->adminA = User::create(['name' => 'Vina', 'username' => 'vina', 'email' => 'vina@mbg.local', 'role' => 'admin_dapur', 'dapur_id' => $this->a->id, 'password' => 'rahasia123']);
        $this->logistik = User::where('username', 'logistik')->first();
        $this->mbInv = MasterBarang::where('jenis', 'inventaris')->first();
        $this->mbBahan = MasterBarang::where('jenis', 'bahan_pokok')->first();
        $this->supplier = Supplier::first();
    }

    private function bahan(Dapur $d, string $sumber = 'logistik', string $tgl = '2026-10-01'): BahanPokok
    {
        return BahanPokok::create(['master_barang_id' => $this->mbBahan->id, 'supplier_id' => $this->supplier->id, 'dapur_id' => $d->id,
            'sumber' => $sumber, 'tanggal_transaksi' => $tgl, 'quantity' => 5, 'harga_per_item' => 10000]);
    }

    private function inv(Dapur $d, string $kondisi = 'baik'): BarangInventaris
    {
        return BarangInventaris::create(['master_barang_id' => $this->mbInv->id, 'dapur_id' => $d->id, 'tanggal_beli' => '2026-09-01',
            'quantity' => 2, 'harga_per_item' => 1000, 'status_barang' => 'barang_tetap', 'kondisi_barang' => $kondisi]);
    }

    // ---------- BIAYA UPDATE KONDISI ----------

    public function test_biaya_update_kondisi_tersimpan_dan_tampil_di_riwayat_dan_management_dapur(): void
    {
        $barang = $this->inv($this->a, 'rusak_ringan');

        $this->actingAs($this->logistik)
            ->put("/logistik/barang-inventaris/{$barang->id}/kondisi", ['kondisi_barang' => 'baik', 'tanggal_update' => '2026-10-05', 'biaya' => 150000, 'catatan' => 'servis'])
            ->assertSessionHasNoErrors();
        $this->put("/logistik/barang-inventaris/{$barang->id}/kondisi", ['kondisi_barang' => 'rusak_berat', 'biaya' => 0, 'tanggal_update' => '2026-10-06'])->assertSessionHasNoErrors();
        $this->put("/logistik/barang-inventaris/{$barang->id}/kondisi", ['kondisi_barang' => 'baru', 'tanggal_update' => '2026-10-07', 'biaya' => 2500000])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('riwayat_kondisi_inventaris', ['barang_inventaris_id' => $barang->id, 'kondisi_sebelum' => 'rusak_ringan', 'kondisi_baru' => 'baik', 'biaya' => 150000]);
        $this->assertDatabaseHas('riwayat_kondisi_inventaris', ['kondisi_sebelum' => 'rusak_berat', 'kondisi_baru' => 'baru', 'biaya' => 2500000]);
        $this->assertDatabaseHas('riwayat_kondisi_inventaris', ['kondisi_baru' => 'rusak_berat', 'biaya' => 0]);
        $this->assertSame(2650000.0, $barang->fresh()->load('riwayatKondisi')->totalBiayaUpdateKondisi());
        $this->assertSame(2650000.0, $this->a->totalBiayaUpdateKondisiInventaris());
        $this->assertSame(0.0, $this->b->totalBiayaUpdateKondisiInventaris());

        $this->put("/logistik/barang-inventaris/{$barang->id}/kondisi", ['kondisi_barang' => 'baik', 'tanggal_update' => '2026-10-07'])->assertSessionHasErrors('biaya'); // wajib
        $this->put("/logistik/barang-inventaris/{$barang->id}/kondisi", ['kondisi_barang' => 'baik', 'tanggal_update' => '2026-10-07', 'biaya' => ''])->assertSessionHasErrors('biaya');
        $this->put("/logistik/barang-inventaris/{$barang->id}/kondisi", ['kondisi_barang' => 'baik', 'tanggal_update' => '2026-10-07', 'biaya' => -5])->assertSessionHasErrors('biaya');
        $this->put("/logistik/barang-inventaris/{$barang->id}/kondisi", ['kondisi_barang' => 'baik', 'tanggal_update' => '2026-10-07', 'biaya' => 'abc'])->assertSessionHasErrors('biaya');

        // Management Dapur: detail + card overview
        $this->get("/dapur/{$this->a->id}")->assertOk()
            ->assertSee('Total Biaya Update Kondisi Inventaris')->assertSee('Rp2.650.000')
            ->assertSee('Riwayat Update Kondisi')->assertSee('Rp150.000')->assertSee($this->logistik->namaDanUsername())->assertDontSee('Kondisi awal');
        $this->get('/dapur')->assertOk()->assertSee('Biaya Update Kondisi Inventaris')->assertSee('Rp2.650.000');

        // Modal memuat biaya & field biaya
        $this->get('/logistik/barang-inventaris')->assertOk()->assertSee('name="biaya"', false);

        // Excel: kolom biaya
        $r = $this->get('/logistik/barang-inventaris/unduh');
        file_put_contents('/tmp/inv_biaya.xlsx', $r->baseResponse->getFile()->getContent());
        $zip = new \ZipArchive();
        $zip->open('/tmp/inv_biaya.xlsx');
        $this->assertStringContainsString('Biaya Update Kondisi', $zip->getFromName('xl/worksheets/sheet2.xml'));
        $this->assertStringContainsString('Total Biaya Update Kondisi', $zip->getFromName('xl/worksheets/sheet1.xml'));
    }

    // ---------- MENU & DASHBOARD ADMIN DAPUR ----------

    public function test_sidebar_admin_dapur_punya_5_menu_tanpa_menu_logistik(): void
    {
        $html = $this->actingAs($this->adminA)->get('/dapur-saya')->assertOk()->getContent();
        foreach (['>Dashboard<', '>Kategori Bahan Pokok<', '>Kategori Barang Inventaris<', '>Bahan Pokok<', '>Barang Inventaris<'] as $menu) {
            $this->assertStringContainsString($menu, $html);
        }
        foreach (['>Supplier<', '>Master Barang<', '>Dapur<', '>Manajemen Akun<'] as $menu) {
            $this->assertStringNotContainsString($menu, $html);
        }
    }

    public function test_dashboard_admin_dapur_berisi_diagram_dan_hanya_data_dapurnya(): void
    {
        $this->bahan($this->a);
        $this->bahan($this->b, 'logistik', '2026-10-02');
        $barang = $this->inv($this->a, 'rusak_ringan');
        $this->inv($this->b);
        $barang->perbaruiKondisi('baik', $this->adminA->id, '2026-10-05', 'servis', biaya: 175000);

        $this->actingAs($this->adminA)->get('/dapur-saya')->assertOk()
            ->assertSee('Dashboard Dapur A')
            ->assertSee('Total Biaya Update Kondisi Inventaris')->assertSee('Rp175.000')
            ->assertSee('Tren Belanja Bahan Pokok')->assertSee('Kondisi Barang Inventaris')
            ->assertSee('Status Laporan Bahan Kiriman')->assertSee('Bahan Belum Dilaporkan')
            ->assertSee('Biaya Update Kondisi Terbesar per Barang')
            ->assertSee('Rp50.000') // total bahan dapur A saja (5 x 10.000), bukan dapur B
            ->assertDontSee('Dapur B');
    }

    // ---------- KATEGORI ----------

    public function test_admin_dapur_bisa_kelola_kategori(): void
    {
        $this->actingAs($this->adminA);
        $this->get('/kategori-bahan-pokok')->assertOk()->assertSee('Kategori Bahan Pokok');
        $this->get('/kategori-barang-inventaris')->assertOk()->assertSee('Kategori Barang Inventaris');
        $this->post('/kategori-barang-inventaris/simpan', ['nama_kategori' => 'Elektronik Dapur'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('kategori_barang', ['nama_kategori' => 'Elektronik Dapur', 'jenis' => 'inventaris']);
        // Tetap tidak boleh membuka menu khusus logistik
        $this->get('/supplier')->assertRedirect();
        $this->get('/master-barang')->assertRedirect();
        $this->get('/dapur')->assertRedirect();
    }

    // ---------- BAHAN POKOK (admin dapur) ----------

    public function test_bahan_pokok_admin_dapur_hanya_dapur_sendiri_dengan_pembelian_mandiri(): void
    {
        $this->bahan($this->a);
        $this->bahan($this->b, 'logistik', '2026-10-02');

        $this->actingAs($this->adminA)->get('/logistik/bahan-pokok')->assertOk()
            ->assertSee('Dapur A')->assertDontSee('Dapur B')
            ->assertSee('Catat Pembelian Mandiri')->assertDontSee('Tambah Transaksi')->assertDontSee('-- Semua Dapur --');

        // Detail milik sendiri OK, milik dapur lain 403
        $this->get('/logistik/bahan-pokok/detail?tanggal=2026-10-01&dapur_id='.$this->a->id)->assertOk()->assertSee('Lapor');
        $this->get('/logistik/bahan-pokok/detail?tanggal=2026-10-02&dapur_id='.$this->b->id)->assertForbidden();
        $this->get('/logistik/bahan-pokok/detail/unduh?tanggal=2026-10-02&dapur_id='.$this->b->id)->assertForbidden();

        // Export semua: dipaksa dapur sendiri
        $r = $this->get('/logistik/bahan-pokok/unduh-semua?dapur_id='.$this->b->id);
        $r->assertOk();
        $this->assertStringContainsString('dapur-a', $r->headers->get('content-disposition'));

        // Tambah Transaksi logistik tidak boleh
        $this->get('/logistik/bahan-pokok/tambah')->assertRedirect();
        $this->post('/logistik/bahan-pokok/simpan-batch', [])->assertRedirect();

        // Pembelian mandiri tetap ada & kembali ke menu Bahan Pokok
        $this->get('/dapur-saya/beli-mandiri')->assertOk();
        $this->post('/dapur-saya/beli-mandiri', ['tanggal_transaksi' => '2026-10-08', 'bahan' => [[
            'master_barang_id' => $this->mbBahan->id, 'supplier_id' => $this->supplier->id, 'quantity' => 2, 'harga_per_item' => 5000,
        ]]])->assertRedirect(route('bahanPokok.indeks'));
        $this->assertDatabaseHas('bahan_pokok', ['dapur_id' => $this->a->id, 'sumber' => 'dapur', 'tanggal_transaksi' => '2026-10-08 00:00:00']);
    }

    public function test_laporan_masih_berfungsi_dan_edit_hapus_hanya_untuk_bahan_mandiri(): void
    {
        $kiriman = $this->bahan($this->a, 'logistik');
        $mandiri = $this->bahan($this->a, 'dapur', '2026-10-03');
        $bahanB = $this->bahan($this->b, 'logistik', '2026-10-02');
        $this->actingAs($this->adminA);

        $this->post('/dapur-saya/laporan', ['bahan_pokok_id' => $kiriman->id, 'status' => 'habis', 'tanggal_lapor' => '2026-10-04'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('laporan_pemakaian_bahan', ['bahan_pokok_id' => $kiriman->id, 'dilaporkan_oleh' => $this->adminA->id]);
        $this->post('/dapur-saya/laporan', ['bahan_pokok_id' => $bahanB->id, 'status' => 'habis', 'tanggal_lapor' => '2026-10-04'])->assertForbidden();

        $data = ['master_barang_id' => $this->mbBahan->id, 'supplier_id' => $this->supplier->id, 'dapur_id' => $this->b->id, 'tanggal_transaksi' => '2026-10-03', 'quantity' => 9, 'harga_per_item' => 1000];
        $this->put("/logistik/bahan-pokok/{$kiriman->id}", $data)->assertForbidden();            // kiriman logistik: tidak boleh
        $this->delete("/logistik/bahan-pokok/{$kiriman->id}")->assertForbidden();
        $this->put("/logistik/bahan-pokok/{$bahanB->id}", $data)->assertForbidden();              // dapur lain
        $this->put("/logistik/bahan-pokok/{$mandiri->id}", $data)->assertSessionHasNoErrors();    // mandiri sendiri: boleh
        $this->assertSame($this->a->id, $mandiri->fresh()->dapur_id);                              // dapur_id tetap dapur sendiri
        $this->assertEquals(9, $mandiri->fresh()->quantity);
        $this->delete("/logistik/bahan-pokok/{$mandiri->id}")->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('bahan_pokok', ['id' => $mandiri->id]);
    }

    // ---------- BARANG INVENTARIS (admin dapur) ----------

    public function test_barang_inventaris_admin_dapur_akses_penuh_tapi_hanya_dapur_sendiri(): void
    {
        $milikA = $this->inv($this->a);
        $milikB = $this->inv($this->b);
        $this->actingAs($this->adminA);

        $this->get('/logistik/barang-inventaris')->assertOk()
            ->assertSee('Export to Excel')->assertSee('Tambah Transaksi')->assertSee('Update Kondisi')
            ->assertDontSee('-- Semua Dapur --')->assertDontSee('Dapur B');
        $this->get('/logistik/barang-inventaris?terpilih='.$milikA->id)->assertOk();
        $this->get('/logistik/barang-inventaris?terpilih='.$milikB->id)->assertOk()->assertDontSee('Dapur B');

        // Tambah: dapur dipaksa dapur sendiri meski form berisi dapur lain
        $this->get('/logistik/barang-inventaris/tambah')->assertOk()->assertSee('Dapur A');
        $this->post('/logistik/barang-inventaris/simpan-batch', [
            'dapur_id' => $this->b->id, 'tanggal_beli' => '2026-10-01',
            'barang' => [['master_barang_id' => $this->mbInv->id, 'quantity' => 1, 'harga_per_item' => 500, 'status_barang' => 'barang_tetap', 'kondisi_barang' => 'baru']],
        ])->assertSessionHasNoErrors();
        $baru = BarangInventaris::latest('id')->first();
        $this->assertSame($this->a->id, $baru->dapur_id);

        // Edit & hapus: miliknya boleh (dapur tidak bisa dipindah), milik dapur lain 403
        $payload = ['master_barang_id' => $this->mbInv->id, 'dapur_id' => $this->b->id, 'tanggal_beli' => '2026-09-01', 'quantity' => 7,
            'harga_per_item' => 1000, 'status_barang' => 'barang_tetap', 'kondisi_barang' => 'baik'];
        $this->put("/logistik/barang-inventaris/{$milikA->id}", $payload)->assertSessionHasNoErrors();
        $this->assertEquals(7, $milikA->fresh()->quantity);
        $this->assertSame($this->a->id, $milikA->fresh()->dapur_id);
        $this->put("/logistik/barang-inventaris/{$milikB->id}", $payload)->assertForbidden();
        $this->delete("/logistik/barang-inventaris/{$milikB->id}")->assertForbidden();
        $this->post('/logistik/barang-inventaris/riwayat', ['barang_inventaris_id' => $milikB->id, 'tanggal' => '2026-10-01', 'jenis_aksi' => 'perawatan', 'biaya' => 100])->assertForbidden();
        $this->delete("/logistik/barang-inventaris/{$milikA->id}")->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('barang_inventaris', ['id' => $milikA->id]);
    }

    public function test_admin_logistik_tetap_bisa_semua_dapur(): void
    {
        $this->bahan($this->a);
        $this->bahan($this->b, 'logistik', '2026-10-02');
        $this->inv($this->a);
        $this->inv($this->b);
        $this->actingAs($this->logistik);
        $this->get('/logistik/bahan-pokok')->assertOk()->assertSee('Dapur A')->assertSee('Dapur B')->assertSee('Tambah Transaksi')->assertSee('-- Semua Dapur --');
        $this->get('/logistik/barang-inventaris')->assertOk()->assertSee('Dapur A')->assertSee('Dapur B');
        $this->get('/dapur-saya')->assertRedirect(); // dashboard dapur khusus admin dapur
    }
}
