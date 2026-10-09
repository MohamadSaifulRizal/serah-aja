<?php

namespace App\Helpers;

class KalkulatorDapur
{
    /**
     * Format angka gaya Indonesia: titik untuk ribuan, koma untuk desimal.
     * Angka bulat tampil tanpa desimal (1000 -> "1.000"), angka pecahan tampil
     * ringkas tanpa nol berlebih (1.2 -> "1,2", bukan "1,20").
     */
    public static function formatAngka($angka, int $desimalMaksimal = 2): string
    {
        $angka = round((float) $angka, $desimalMaksimal);
        $teks = number_format($angka, $desimalMaksimal, ',', '.');

        if ($desimalMaksimal > 0) {
            $teks = rtrim($teks, '0');
            $teks = rtrim($teks, ',');
        }

        return $teks;
    }

    /**
     * Format nilai Rupiah gaya Indonesia, contoh: Rp12.000
     */
    public static function formatRupiah($angka): string
    {
        return 'Rp' . number_format((float) $angka, 0, ',', '.');
    }
}
