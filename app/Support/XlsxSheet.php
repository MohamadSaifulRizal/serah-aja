<?php

namespace App\Support;

use DateTimeInterface;

/**
 * Satu lembar (sheet) untuk XlsxWriter. Tidak dipakai langsung - buat lewat
 * XlsxWriter::sheet('Nama Sheet').
 *
 * Tipe sel (parameter $tipe di baris()):
 *  teks | tengah (teks rata tengah, mis. No) | angka | uang (Rp) | tanggal
 */
class XlsxSheet
{
    // Index style ini harus sama dengan urutan <cellXfs> di XlsxWriter::stylesXml().
    public const S_DEFAULT = 0;
    public const S_JUDUL = 1;
    public const S_META = 2;
    public const S_HEADER = 3;
    public const S_TEKS = 4;
    public const S_ANGKA = 5;
    public const S_UANG = 6;
    public const S_TANGGAL = 7;
    public const S_TENGAH = 8;
    public const S_TOTAL_LABEL = 9;
    public const S_TOTAL_UANG = 10;
    public const S_BOLD = 11;

    private const TIPE_KE_STYLE = [
        'teks' => self::S_TEKS,
        'tengah' => self::S_TENGAH,
        'angka' => self::S_ANGKA,
        'uang' => self::S_UANG,
        'tanggal' => self::S_TANGGAL,
    ];

    private array $baris = [];
    private array $lebarKolom = [];
    private int $bekukanSampaiBaris = 0;

    public function __construct(public readonly string $nama)
    {
    }

    public function lebarKolom(array $lebar): static
    {
        $this->lebarKolom = array_values($lebar);

        return $this;
    }

    public function judul(string $teks): static
    {
        return $this->tambah([[$teks, self::S_JUDUL]]);
    }

    public function meta(string $teks): static
    {
        return $this->tambah([[$teks, self::S_META]]);
    }

    public function kosong(): static
    {
        $this->baris[] = [];

        return $this;
    }

    public function header(array $kolom): static
    {
        $this->tambah(array_map(fn ($k) => [$k, self::S_HEADER], $kolom));
        // Baris header ikut "dibekukan" supaya tetap terlihat saat scroll ke bawah.
        $this->bekukanSampaiBaris = count($this->baris);

        return $this;
    }

    /**
     * @param  array  $nilai  isi tiap kolom
     * @param  array  $tipe   tipe tiap kolom (lihat daftar di atas); kolom tanpa tipe dianggap 'teks'
     */
    public function baris(array $nilai, array $tipe = []): static
    {
        $sel = [];
        foreach (array_values($nilai) as $i => $v) {
            $sel[] = [$v, self::TIPE_KE_STYLE[$tipe[$i] ?? 'teks'] ?? self::S_TEKS];
        }

        return $this->tambah($sel);
    }

    // Baris total: label di kolom $kolomLabel (rata kanan), angka total di kolom $kolomNilai.
    public function barisTotal(int $jumlahKolom, int $kolomLabel, string $label, int $kolomNilai, float|int $nilai): static
    {
        $sel = [];
        for ($i = 0; $i < $jumlahKolom; $i++) {
            if ($i === $kolomLabel) {
                $sel[] = [$label, self::S_TOTAL_LABEL];
            } elseif ($i === $kolomNilai) {
                $sel[] = [$nilai, self::S_TOTAL_UANG];
            } else {
                $sel[] = [null, self::S_TOTAL_LABEL];
            }
        }

        return $this->tambah($sel);
    }

    private function tambah(array $sel): static
    {
        $this->baris[] = $sel;

        return $this;
    }

    public function xml(): string
    {
        $maxKolom = max(1, ...array_map('count', $this->baris ?: [[]]));
        $maxBaris = max(1, count($this->baris));

        $x = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $x .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $x .= '<dimension ref="A1:'.self::namaKolom($maxKolom - 1).$maxBaris.'"/>';

        $x .= '<sheetViews><sheetView workbookViewId="0" showGridLines="0">';
        if ($this->bekukanSampaiBaris > 0) {
            $n = $this->bekukanSampaiBaris;
            $x .= '<pane ySplit="'.$n.'" topLeftCell="A'.($n + 1).'" activePane="bottomLeft" state="frozen"/>';
        }
        $x .= '</sheetView></sheetViews>';
        $x .= '<sheetFormatPr defaultRowHeight="15"/>';

        if ($this->lebarKolom) {
            $x .= '<cols>';
            foreach ($this->lebarKolom as $i => $w) {
                $x .= '<col min="'.($i + 1).'" max="'.($i + 1).'" width="'.$w.'" customWidth="1"/>';
            }
            $x .= '</cols>';
        }

        $x .= '<sheetData>';
        foreach ($this->baris as $r => $sel) {
            $noBaris = $r + 1;
            $x .= '<row r="'.$noBaris.'">';
            foreach ($sel as $c => [$nilai, $style]) {
                $x .= $this->selXml(self::namaKolom($c).$noBaris, $nilai, $style);
            }
            $x .= '</row>';
        }
        $x .= '</sheetData>';
        $x .= '<pageMargins left="0.5" right="0.5" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>';
        $x .= '<pageSetup orientation="landscape" fitToHeight="0"/>';
        $x .= '</worksheet>';

        return $x;
    }

    private function selXml(string $ref, mixed $nilai, int $style): string
    {
        if ($nilai === null || $nilai === '') {
            return '<c r="'.$ref.'" s="'.$style.'"/>';
        }

        if ($style === self::S_TANGGAL) {
            $serial = self::serialTanggal($nilai);
            if ($serial !== null) {
                return '<c r="'.$ref.'" s="'.$style.'"><v>'.$serial.'</v></c>';
            }
        }

        if (is_int($nilai) || is_float($nilai) || (is_numeric($nilai) && in_array($style, [self::S_ANGKA, self::S_UANG, self::S_TOTAL_UANG], true))) {
            return '<c r="'.$ref.'" s="'.$style.'"><v>'.(0 + $nilai).'</v></c>';
        }

        return '<c r="'.$ref.'" s="'.$style.'" t="inlineStr"><is><t xml:space="preserve">'.self::esc((string) $nilai).'</t></is></c>';
    }

    // Excel menyimpan tanggal sebagai jumlah hari sejak 30 Des 1899.
    private static function serialTanggal(mixed $nilai): ?int
    {
        try {
            $tgl = $nilai instanceof DateTimeInterface ? $nilai : new \DateTimeImmutable((string) $nilai);
            $dasar = new \DateTimeImmutable('1899-12-30');

            return (int) $dasar->diff(new \DateTimeImmutable($tgl->format('Y-m-d')))->format('%r%a');
        } catch (\Throwable) {
            return null;
        }
    }

    public static function namaKolom(int $index): string
    {
        $nama = '';
        for ($i = $index + 1; $i > 0; $i = intdiv($i - 1, 26)) {
            $nama = chr(65 + (($i - 1) % 26)).$nama;
        }

        return $nama;
    }

    public static function esc(string $teks): string
    {
        // Buang karakter kontrol yang tidak valid di XML 1.0, lalu escape.
        $teks = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $teks) ?? '';

        return htmlspecialchars($teks, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
