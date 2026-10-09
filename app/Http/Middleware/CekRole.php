<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    /**
     * Batasi akses route berdasarkan role. Dipakai di routes/web.php seperti:
     * ->middleware('role:admin_logistik')  -> cuma admin_logistik yang boleh masuk
     * ->middleware('role:admin_dapur')     -> cuma admin_dapur yang boleh masuk
     *
     * Kalau role tidak cocok, dilempar ke dashboard masing-masing (bukan error 403 polos),
     * supaya user awam tidak bingung - langsung diarahkan ke halaman yang memang haknya dia.
     */
    public function handle(Request $request, Closure $next, string ...$rolesDiizinkan): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $rolesDiizinkan, true)) {
            // Belum login -> ke halaman login; sudah login -> ke halaman awal sesuai role-nya.
            $tujuan = $user ? $user->rutePerdana() : route('login');

            return redirect($tujuan)->with('error', 'Anda tidak punya akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
