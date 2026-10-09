<?php

namespace Tests\Feature;

use Tests\TestCase;

class TailwindV4Test extends TestCase
{
    public function test_layout_memakai_tailwind_v4_bukan_v3(): void
    {
        $html = $this->get('/login')->assertOk()->getContent();
        $this->assertStringContainsString('@tailwindcss/browser@4', $html);
        $this->assertStringContainsString('--color-green-deep', $html);
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $html);
        $this->assertStringNotContainsString('tailwind.config', $html);
    }

    // Menjaga agar peringatan editor Tailwind v4 (mis. min-w-[640px] -> min-w-160) tidak muncul lagi.
    public function test_tidak_ada_kelas_arbitrary_yang_punya_padanan_skala_v4(): void
    {
        $pola = '/(?<![\w-])-?(?:min-w|max-w|min-h|max-h|w|h|size|gap|leading|[mp][trblxy]?)-\[\d+(?:\.\d+)?px\]|(?<![\w-])z-\[\d+\]|flex-shrink-0|flex-grow(?![\w-])|bg-gradient-to-|(?<![\w-])outline-none/';
        $temuan = [];
        foreach (glob(resource_path('views/{,*/}*.blade.php'), GLOB_BRACE) as $file) {
            // welcome.blade.php hanya halaman bawaan Laravel (CSS hasil build), tidak dipakai aplikasi
            if (str_ends_with($file, 'welcome.blade.php')) {
                continue;
            }
            if (preg_match_all($pola, file_get_contents($file), $m)) {
                $temuan[basename($file)] = array_unique($m[0]);
            }
        }
        $this->assertSame([], $temuan, 'Masih ada kelas Tailwind versi lama / arbitrary: '.json_encode($temuan));
    }
}
