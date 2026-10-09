<?php

namespace Tests\Feature;

use App\Models\BahanPokok;
use App\Models\BarangInventaris;
use App\Models\Dapur;
use App\Models\KategoriBarang;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KategoriInventarisExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::where('username', 'logistik')->first());
    }

    public function test_dua_menu_kategori_terpisah_per_jenis(): void
    {
        $this->get('/kategori-bahan-pokok')->assertOk()
            ->assertSee('Kategori Bahan Pokok')->assertSee('Sembako')->assertDontSee('Peralatan Masak');
        $this->get('/kategori-barang-inventaris')->assertOk()
            ->assertSee('Kategori Barang Inventaris')->assertSee('Peralatan Masak')->assertDontSee('Sembako');

        $this->post('/kategori-barang-inventaris/simpan', ['nama_kategori' => 'Elektronik'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('kategori_barang', ['nama_kategori' => 'Elektronik', 'jenis' => 'inventaris']);

        // Nama sama boleh di jenis berbeda, tapi tidak boleh dobel di jenis yang sama.
        $this->post('/kategori-bahan-pokok/simpan', ['nama_kategori' => 'Elektronik'])->assertSessionHasNoErrors();
        $this->post('/kategori-bahan-pokok/simpan', ['nama_kategori' => 'Elektronik'])->assertSessionHasErrors('nama_kategori');

        // Kategori Inventaris tidak bisa diedit lewat URL menu Bahan Pokok.
        $k = KategoriBarang::where('jenis', 'inventaris')->first();
        $this->put("/kategori-bahan-pokok/{$k->id}", ['nama_kategori' => 'X'])->assertNotFound();
    }

    public function test_sidebar_tanpa_dropdown_logistik(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringNotContainsString('grupAktif', $html);
        foreach (['Kategori Bahan Pokok', 'Kategori Barang Inventaris', '>Bahan Pokok<', '>Barang Inventaris<'] as $t) {
            $this->assertStringContainsString($t, $html);
        }
        $this->assertStringNotContainsString('>Logistik<', $html);
    }

    public function test_master_barang_kategori_harus_sesuai_jenis(): void
    {
        $katInv = KategoriBarang::where('jenis', 'inventaris')->first();
        $this->post('/master-barang/simpan', ['jenis' => 'bahan_pokok', 'nama_barang' => 'Gula', 'kategori_id' => $katInv->id, 'satuan' => 'kg'])
            ->assertSessionHasErrors('kategori_id');
        $this->post('/master-barang/simpan', ['jenis' => 'inventaris', 'nama_barang' => 'Kulkas', 'kategori_id' => $katInv->id])
            ->assertSessionHasNoErrors();
        $this->get('/master-barang')->assertOk();
    }

    public function test_inventaris_kondisi_tampil_dan_tersimpan(): void
    {
        $dapur = Dapur::first();
        $mb = \App\Models\MasterBarang::where('jenis', 'inventaris')->first();

        $this->post('/logistik/barang-inventaris/simpan-batch', [
            'dapur_id' => $dapur->id, 'tanggal_beli' => '2026-10-01',
            'barang' => [['master_barang_id' => $mb->id, 'quantity' => 2, 'harga_per_item' => 1000, 'status_barang' => 'barang_tetap']],
        ])->assertSessionHasErrors('barang.0.kondisi_barang');

        $this->post('/logistik/barang-inventaris/simpan-batch', [
            'dapur_id' => $dapur->id, 'tanggal_beli' => '2026-10-01',
            'barang' => [['master_barang_id' => $mb->id, 'quantity' => 2, 'harga_per_item' => 1000, 'status_barang' => 'barang_tetap', 'kondisi_barang' => 'rusak_berat']],
        ])->assertSessionHasNoErrors();
        $b = BarangInventaris::latest('id')->first();
        $this->assertSame('rusak_berat', $b->kondisi_barang);

        $this->put("/logistik/barang-inventaris/{$b->id}", [
            'master_barang_id' => $mb->id, 'dapur_id' => $dapur->id, 'tanggal_beli' => '2026-10-01', 'quantity' => 2,
            'harga_per_item' => 1000, 'status_barang' => 'barang_tetap', 'kondisi_barang' => 'rusak_ringan',
        ])->assertSessionHasNoErrors();
        $this->assertSame('rusak_ringan', $b->fresh()->kondisi_barang);

        $this->get('/logistik/barang-inventaris')->assertOk()
            ->assertSee('Dapur Pemilik')->assertSee('Kondisi Barang')->assertSee('Rusak Ringan')->assertSee('Export to Excel');
        $this->get('/logistik/barang-inventaris?terpilih='.$b->id)->assertOk();
        $this->get('/logistik/barang-inventaris/tambah')->assertOk()->assertSee('Kondisi Barang');
        $this->get('/dapur/'.$dapur->id)->assertOk()->assertSee('Kondisi');
    }

    public function test_export_excel_inventaris_semua_dan_per_dapur(): void
    {
        $d2 = Dapur::where('nama_dapur', 'Dapur Melati')->first();
        $mb = \App\Models\MasterBarang::where('jenis', 'inventaris')->first();
        BarangInventaris::create(['master_barang_id' => $mb->id, 'dapur_id' => $d2->id, 'tanggal_beli' => '2026-09-01', 'quantity' => 3, 'harga_per_item' => 500, 'status_barang' => 'barang_tetap', 'kondisi_barang' => 'baru']);

        $semua = $this->get('/logistik/barang-inventaris/unduh');
        $semua->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', $semua->headers->get('content-type'));
        $this->assertStringContainsString('semua-dapur', $semua->headers->get('content-disposition'));
        file_put_contents('/tmp/inv_semua.xlsx', $semua->baseResponse->getFile()->getContent());

        $satu = $this->get('/logistik/barang-inventaris/unduh?dapur_id='.$d2->id);
        $satu->assertOk();
        $this->assertStringContainsString('dapur-melati', $satu->headers->get('content-disposition'));
        file_put_contents('/tmp/inv_melati.xlsx', $satu->baseResponse->getFile()->getContent());

        $this->get('/logistik/barang-inventaris/unduh?dapur_id=9999')->assertSessionHasErrors('dapur_id');
    }

    public function test_export_bahan_pokok_excel_bukan_csv(): void
    {
        $t = BahanPokok::first();
        $r = $this->get('/logistik/bahan-pokok/detail/unduh?tanggal='.$t->tanggal_transaksi->format('Y-m-d').'&dapur_id='.$t->dapur_id);
        $r->assertOk();
        $this->assertStringContainsString('.xlsx', $r->headers->get('content-disposition'));
        file_put_contents('/tmp/bp_detail.xlsx', $r->baseResponse->getFile()->getContent());

        $this->get('/logistik/bahan-pokok/detail?tanggal='.$t->tanggal_transaksi->format('Y-m-d').'&dapur_id='.$t->dapur_id)
            ->assertOk()->assertSee('Export to Excel')->assertDontSee('CSV');

        $semua = $this->get('/logistik/bahan-pokok/unduh-semua');
        $semua->assertOk();
        $this->assertStringContainsString('transaksi-bahan-pokok-semua-dapur', $semua->headers->get('content-disposition'));
        file_put_contents('/tmp/bp_semua.xlsx', $semua->baseResponse->getFile()->getContent());

        $this->get('/logistik/bahan-pokok')->assertOk()->assertSee('Export Semua Transaksi');
        $this->get('/logistik/bahan-pokok/tambah')->assertOk();
    }
}
