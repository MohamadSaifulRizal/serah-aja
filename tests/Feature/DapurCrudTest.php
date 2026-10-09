<?php

namespace Tests\Feature;

use App\Models\Dapur;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DapurCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::where('username', 'logistik')->first());
    }

    public function test_alamat_dan_penanggung_jawab_wajib_saat_tambah(): void
    {
        $this->post('/dapur', ['nama_dapur' => 'Dapur Baru'])
            ->assertSessionHasErrors(['alamat', 'penanggung_jawab']);
        $this->post('/dapur', ['nama_dapur' => 'Dapur Baru', 'alamat' => '  ', 'penanggung_jawab' => ''])
            ->assertSessionHasErrors(['alamat', 'penanggung_jawab']);
        $this->assertDatabaseMissing('dapur', ['nama_dapur' => 'Dapur Baru']);

        $this->post('/dapur', ['nama_dapur' => 'Dapur Baru', 'alamat' => 'Jl. A', 'penanggung_jawab' => 'Pak B'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('dapur', ['nama_dapur' => 'Dapur Baru', 'alamat' => 'Jl. A']);
    }

    public function test_edit_dapur_wajib_alamat_dan_pj(): void
    {
        $d = Dapur::create(['nama_dapur' => 'Dapur X', 'alamat' => 'A', 'penanggung_jawab' => 'P']);

        $this->put("/dapur/{$d->id}", ['nama_dapur' => 'Dapur X2', 'alamat' => '', 'penanggung_jawab' => ''])
            ->assertSessionHasErrors(['alamat', 'penanggung_jawab']);
        $this->assertSame('Dapur X', $d->fresh()->nama_dapur);

        $this->put("/dapur/{$d->id}", ['nama_dapur' => 'Dapur X2', 'alamat' => 'Alamat Baru', 'penanggung_jawab' => 'PJ Baru', 'catatan' => 'ok'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('dapur', ['id' => $d->id, 'nama_dapur' => 'Dapur X2', 'alamat' => 'Alamat Baru', 'penanggung_jawab' => 'PJ Baru']);
    }

    public function test_hapus_dapur_kosong_berhasil(): void
    {
        $d = Dapur::create(['nama_dapur' => 'Dapur Kosong', 'alamat' => 'A', 'penanggung_jawab' => 'P']);
        $this->delete("/dapur/{$d->id}")->assertSessionHasNoErrors()->assertSessionHas('success');
        $this->assertDatabaseMissing('dapur', ['id' => $d->id]);
    }

    public function test_hapus_dapur_ditolak_kalau_punya_transaksi_atau_akun(): void
    {
        $punyaTransaksi = Dapur::where('nama_dapur', 'Dapur Melati')->first();   // punya bahan pokok
        $this->delete("/dapur/{$punyaTransaksi->id}")->assertSessionHasErrors('hapus');
        $this->assertDatabaseHas('dapur', ['id' => $punyaTransaksi->id]);

        $punyaAkun = Dapur::create(['nama_dapur' => 'Dapur Akun', 'alamat' => 'A', 'penanggung_jawab' => 'P']);
        User::create(['name' => 'AD', 'username' => 'ad_x', 'email' => 'adx@mbg.local', 'role' => 'admin_dapur', 'dapur_id' => $punyaAkun->id, 'password' => bcrypt('x')]);
        $this->delete("/dapur/{$punyaAkun->id}")->assertSessionHasErrors('hapus');
        $this->assertDatabaseHas('dapur', ['id' => $punyaAkun->id]);
    }

    public function test_halaman_dapur_menampilkan_tombol_edit_hapus(): void
    {
        $this->get('/dapur')->assertOk()
            ->assertSee('Edit Dapur')->assertSee('Hapus')->assertSee('Simpan Perubahan')
            ->assertSee('Dapur Sukamaju');
    }
}
