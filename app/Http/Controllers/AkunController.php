<?php

namespace App\Http\Controllers;

use App\Models\Dapur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Manajemen Akun - HANYA bisa diakses Super Admin (dijaga middleware role:super_admin
 * di routes/web.php). Super admin membuat/mengubah/menghapus akun Admin Logistik dan
 * Admin Dapur. Akun Super Admin sendiri tidak dibuat dari sini (lewat seeder / artisan).
 */
class AkunController extends Controller
{
    // Role yang boleh dibuat lewat menu ini.
    private const ROLE_DAPAT_DIBUAT = [User::ROLE_ADMIN_LOGISTIK, User::ROLE_ADMIN_DAPUR];

    public function indeks()
    {
        $daftarAkun = User::with('dapur')
            ->orderByRaw("CASE role WHEN 'super_admin' THEN 0 WHEN 'admin_logistik' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->get();
        $daftarDapur = Dapur::orderBy('nama_dapur')->get();

        return view('halaman.akun_indeks', compact('daftarAkun', 'daftarDapur'));
    }

    public function simpan(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'role' => ['required', Rule::in(self::ROLE_DAPAT_DIBUAT)],
            'dapur_id' => ['nullable', 'required_if:role,'.User::ROLE_ADMIN_DAPUR, 'exists:dapur,id'],
            'password' => 'required|string|min:6',
        ], [
            'username.unique' => 'Username sudah dipakai akun lain.',
            'dapur_id.required_if' => 'Admin Dapur wajib dikaitkan ke satu dapur.',
        ]);

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            // Login pakai username; email diisi otomatis agar memenuhi kolom unik.
            'email' => $data['username'].'@mbg.local',
            'role' => $data['role'],
            'dapur_id' => $data['role'] === User::ROLE_ADMIN_DAPUR ? $data['dapur_id'] : null,
            'password' => $data['password'], // di-hash otomatis oleh cast 'hashed'
        ]);

        return back()->with('success', "Akun {$data['name']} ({$data['username']}) berhasil dibuat!");
    }

    public function update(Request $request, User $akun)
    {
        // Akun super admin tidak diubah lewat menu ini (supaya tidak terkunci sendiri).
        if ($akun->isSuperAdmin()) {
            return back()->withErrors(['akun' => 'Akun Super Admin tidak dapat diubah dari menu ini.']);
        }

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($akun->id)],
            'role' => ['required', Rule::in(self::ROLE_DAPAT_DIBUAT)],
            'dapur_id' => ['nullable', 'required_if:role,'.User::ROLE_ADMIN_DAPUR, 'exists:dapur,id'],
            'password' => 'nullable|string|min:6',
        ], [
            'username.unique' => 'Username sudah dipakai akun lain.',
            'dapur_id.required_if' => 'Admin Dapur wajib dikaitkan ke satu dapur.',
        ]);

        $akun->fill([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['username'].'@mbg.local',
            'role' => $data['role'],
            'dapur_id' => $data['role'] === User::ROLE_ADMIN_DAPUR ? $data['dapur_id'] : null,
        ]);

        // Password hanya diganti kalau diisi.
        if (! empty($data['password'])) {
            $akun->password = $data['password'];
        }

        $akun->save();

        return back()->with('success', "Akun {$akun->name} berhasil diperbarui!");
    }

    public function hapus(Request $request, User $akun)
    {
        if ($akun->id === $request->user()->id) {
            return back()->withErrors(['akun' => 'Anda tidak dapat menghapus akun yang sedang dipakai.']);
        }

        if ($akun->isSuperAdmin()) {
            return back()->withErrors(['akun' => 'Akun Super Admin tidak dapat dihapus dari menu ini.']);
        }

        $nama = $akun->name;
        $akun->delete();

        return back()->with('success', "Akun {$nama} berhasil dihapus!");
    }
}
