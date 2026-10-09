<?php

namespace Tests\Feature;

use App\Models\BarangInventaris;
use App\Models\Dapur;
use App\Models\MasterBarang;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventarisKondisiUpdateTest extends TestCase
{
    use RefreshDatabase;

    private Dapur $dapurA;
    private Dapur $dapurB;
    private BarangInventaris $barangA;
    private BarangInventaris $barangB;
    private User $adminDapurA;
    private User $logistik;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->dapurA = Dapur::create(['nama_dapur' => 'Dapur A', 'alamat' => 'Jl A', 'penanggung_jawab' => 'PJ A']);
        $this->dapurB = Dapur::create(['nama_dapur' => 'Dapur B', 'alamat' => 'Jl B', 'penanggung_jawab' => 'PJ B']);
        $mb = MasterBarang::where('jenis', 'inventaris')->first();

        $buat = fn (Dapur $d) => BarangInventaris::create([
            'master_barang_id' => $mb->id, 'dapur_id' => $d->id, 'tanggal_beli' => '2026-09-01',
            'quantity' => 1, 'harga_per_item' => 1000, 'status_barang' => 'barang_tetap', 'kondisi_barang' => 'baik',
        ]);
        $this->barangA = $buat($this->dapurA);
        $this->barangB = $buat($this->dapurB);

        $this->adminDapurA = User::create(['name' => 'Siti Admin A', 'username' => 'siti_a', 'email' => 'siti@mbg.local', 'role' => 'admin_dapur', 'dapur_id' => $this->dapurA->id, 'password' => 'rahasia123']);
        $this->logistik = User::where('username', 'logistik')->first();
    }

    public function test_logistik_update_kondisi_tercatat_tanggal_dan_pengupdate(): void
    {
        $this->actingAs($this->logistik)
            ->put("/logistik/barang-inventaris/{$this->barangA->id}/kondisi", ['kondisi_barang' => 'rusak_ringan', 'biaya' => 0, 'tanggal_update' => '2026-10-05', 'catatan' => 'Pintu kendor'])
            ->assertSessionHasNoErrors();

        $b = $this->barangA->fresh();
        $this->assertSame('rusak_ringan', $b->kondisi_barang);
        $this->assertSame('2026-10-05', $b->kondisi_diperbarui_pada->toDateString());
        $this->assertSame($this->logistik->id, $b->kondisi_diperbarui_oleh);
        $this->assertDatabaseHas('riwayat_kondisi_inventaris', [
            'barang_inventaris_id' => $b->id, 'kondisi_sebelum' => 'baik', 'kondisi_baru' => 'rusak_ringan',
            'diperbarui_oleh' => $this->logistik->id, 'catatan' => 'Pintu kendor',
        ]);

        $this->get('/logistik/barang-inventaris')->assertOk()
            ->assertSee('05-10-2026 oleh '.$this->logistik->namaDanUsername())->assertSee('Update Kondisi');
        $this->get('/logistik/barang-inventaris?terpilih='.$b->id)->assertOk()->assertSee('Kondisi Terakhir Diupdate');
    }

    public function test_validasi_kondisi_dan_tanggal(): void
    {
        $this->actingAs($this->logistik);
        $url = "/logistik/barang-inventaris/{$this->barangA->id}/kondisi";
        $this->put($url, ['kondisi_barang' => 'hancur', 'biaya' => 0, 'tanggal_update' => '2026-10-05'])->assertSessionHasErrors('kondisi_barang');
        $this->put($url, ['kondisi_barang' => 'baik', 'tanggal_update' => now()->addDays(3)->toDateString()])->assertSessionHasErrors('tanggal_update');
        $this->put($url, ['kondisi_barang' => 'baik'])->assertSessionHasErrors('tanggal_update');
    }

    public function test_admin_dapur_hanya_bisa_update_barang_dapurnya_sendiri(): void
    {
        $this->actingAs($this->adminDapurA);

        $this->put("/logistik/barang-inventaris/{$this->barangA->id}/kondisi", ['kondisi_barang' => 'rusak_berat', 'biaya' => 0, 'tanggal_update' => '2026-10-06'])
            ->assertSessionHasNoErrors();
        $this->assertSame('rusak_berat', $this->barangA->fresh()->kondisi_barang);
        $this->assertSame($this->adminDapurA->id, $this->barangA->fresh()->kondisi_diperbarui_oleh);

        // Barang dapur lain: ditolak.
        $this->put("/logistik/barang-inventaris/{$this->barangB->id}/kondisi", ['kondisi_barang' => 'rusak_berat', 'biaya' => 0, 'tanggal_update' => '2026-10-06'])->assertForbidden();
        $this->assertSame('baik', $this->barangB->fresh()->kondisi_barang);
    }

    public function test_export_admin_dapur_hanya_dapur_sendiri(): void
    {
        // Walau ?dapur_id dipaksa ke dapur lain, hasilnya tetap dapur sendiri.
        $r = $this->actingAs($this->adminDapurA)->get('/logistik/barang-inventaris/unduh?dapur_id='.$this->dapurB->id);
        $r->assertOk();
        $this->assertStringContainsString('dapur-a', $r->headers->get('content-disposition'));
        $path = '/tmp/inv_dapur_a.xlsx';
        file_put_contents($path, $r->baseResponse->getFile()->getContent());

        $zip = new \ZipArchive();
        $zip->open($path);
        $sheet1 = $zip->getFromName('xl/worksheets/sheet1.xml');
        $this->assertStringContainsString('Dapur A', $sheet1);
        $this->assertStringNotContainsString('Dapur B', $sheet1);
        $this->assertStringNotContainsString('Dapur Melati', $sheet1);
    }

    public function test_edit_data_yang_mengubah_kondisi_tetap_dicatat_dan_input_baru_punya_kondisi_awal(): void
    {
        $this->actingAs($this->logistik);
        $mb = MasterBarang::where('jenis', 'inventaris')->first();

        $this->put("/logistik/barang-inventaris/{$this->barangA->id}", [
            'master_barang_id' => $mb->id, 'dapur_id' => $this->dapurA->id, 'tanggal_beli' => '2026-09-01', 'quantity' => 1,
            'harga_per_item' => 1000, 'status_barang' => 'barang_tetap', 'kondisi_barang' => 'rusak_ringan',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('riwayat_kondisi_inventaris', ['barang_inventaris_id' => $this->barangA->id, 'kondisi_sebelum' => 'baik', 'kondisi_baru' => 'rusak_ringan', 'diperbarui_oleh' => $this->logistik->id]);

        $this->post('/logistik/barang-inventaris/simpan-batch', [
            'dapur_id' => $this->dapurB->id, 'tanggal_beli' => '2026-10-01',
            'barang' => [['master_barang_id' => $mb->id, 'quantity' => 1, 'harga_per_item' => 500, 'status_barang' => 'barang_tetap', 'kondisi_barang' => 'baru']],
        ])->assertSessionHasNoErrors();
        $baru = BarangInventaris::latest('id')->first();
        $this->assertDatabaseHas('riwayat_kondisi_inventaris', ['barang_inventaris_id' => $baru->id, 'kondisi_sebelum' => null, 'kondisi_baru' => 'baru', 'diperbarui_oleh' => $this->logistik->id]);
        $this->assertNotNull($baru->fresh()->kondisi_diperbarui_pada);
    }

    public function test_export_logistik_memuat_kolom_update_dan_sheet_riwayat(): void
    {
        $this->actingAs($this->logistik)->put("/logistik/barang-inventaris/{$this->barangA->id}/kondisi", ['kondisi_barang' => 'rusak_ringan', 'biaya' => 0, 'tanggal_update' => '2026-10-05', 'catatan' => 'uji']);
        $r = $this->get('/logistik/barang-inventaris/unduh');
        $path = '/tmp/inv_logistik_baru.xlsx';
        file_put_contents($path, $r->baseResponse->getFile()->getContent());
        $zip = new \ZipArchive();
        $zip->open($path);
        $this->assertStringContainsString('Diupdate Oleh', $zip->getFromName('xl/worksheets/sheet1.xml'));
        $this->assertStringContainsString('Riwayat Kondisi', $zip->getFromName('xl/workbook.xml'));
        $this->assertStringContainsString($this->logistik->namaDanUsername(), $zip->getFromName('xl/worksheets/sheet2.xml'));
    }
}
