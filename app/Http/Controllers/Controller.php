<?php

namespace App\Http\Controllers;

use App\Models\Dapur;
use Illuminate\Http\Request;

abstract class Controller
{
    // Dapur milik akun kalau yang login Admin Dapur (aksesnya DIBATASI ke dapur itu
    // saja); null untuk Admin Logistik / Super Admin yang boleh mengakses semua dapur.
    protected function dapurTerbatas(Request $request): ?Dapur
    {
        $user = $request->user();

        if (! $user->isAdminDapur()) {
            return null;
        }

        abort_if(! $user->dapur, 403, 'Akun ini belum dikaitkan ke dapur manapun. Hubungi Super Admin.');

        return $user->dapur;
    }
}
