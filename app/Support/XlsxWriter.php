<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

/**
 * Pembuat file Excel (.xlsx) sederhana TANPA library tambahan (cuma butuh
 * ekstensi PHP `zip`, yang sudah dipakai Composer/Laravel pada umumnya).
 * Cukup untuk laporan tabel: judul, header berwarna, angka/Rp/tanggal, total.
 *
 * Contoh:
 *   $x = new XlsxWriter();
 *   $x->sheet('Data')->judul('Laporan')->header(['No','Nama'])->baris([1,'Beras'], ['tengah','teks']);
 *   return $x->unduh('laporan.xlsx');
 */
class XlsxWriter
{
    /** @var XlsxSheet[] */
    private array $sheets = [];

    public function sheet(string $nama): XlsxSheet
    {
        // Aturan Excel: maks 31 karakter, tanpa : \ / ? * [ ] dan nama tidak boleh kembar.
        $bersih = trim(preg_replace('/[:\\\\\/\?\*\[\]]/', ' ', $nama)) ?: 'Sheet';
        $bersih = mb_substr($bersih, 0, 31);
        $asli = $bersih;
        $n = 2;
        while (in_array($bersih, array_map(fn ($s) => $s->nama, $this->sheets), true)) {
            $akhiran = ' ('.$n++.')';
            $bersih = mb_substr($asli, 0, 31 - mb_strlen($akhiran)).$akhiran;
        }

        return $this->sheets[] = new XlsxSheet($bersih);
    }

    // Tulis ke file sementara & kembalikan path-nya.
    public function simpanSementara(): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi PHP "zip" belum aktif - dibutuhkan untuk membuat file Excel.');
        }
        if (! $this->sheets) {
            $this->sheet('Sheet1');
        }

        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Gagal membuat file Excel sementara.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->relsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        foreach ($this->sheets as $i => $sheet) {
            $zip->addFromString('xl/worksheets/sheet'.($i + 1).'.xml', $sheet->xml());
        }
        $zip->close();

        return $path;
    }

    // Kirim sebagai unduhan browser lalu hapus file sementaranya.
    public function unduh(string $namaFile)
    {
        return response()->download($this->simpanSementara(), $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function contentTypesXml(): string
    {
        $x = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $x .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">';
        $x .= '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';
        $x .= '<Default Extension="xml" ContentType="application/xml"/>';
        $x .= '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>';
        $x .= '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
        foreach ($this->sheets as $i => $_) {
            $x .= '<Override PartName="/xl/worksheets/sheet'.($i + 1).'.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return $x.'</Types>';
    }

    private function relsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private function workbookXml(): string
    {
        $x = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $x .= '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>';
        foreach ($this->sheets as $i => $sheet) {
            $x .= '<sheet name="'.XlsxSheet::esc($sheet->nama).'" sheetId="'.($i + 1).'" r:id="rId'.($i + 1).'"/>';
        }

        return $x.'</sheets></workbook>';
    }

    private function workbookRelsXml(): string
    {
        $x = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $x .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        foreach ($this->sheets as $i => $_) {
            $x .= '<Relationship Id="rId'.($i + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet'.($i + 1).'.xml"/>';
        }
        $x .= '<Relationship Id="rId'.(count($this->sheets) + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        return $x.'</Relationships>';
    }

    // Urutan <xf> di <cellXfs> = konstanta S_* di XlsxSheet.
    private function stylesXml(): string
    {
        $b = '<border><left style="thin"><color rgb="FFB8B8A8"/></left><right style="thin"><color rgb="FFB8B8A8"/></right><top style="thin"><color rgb="FFB8B8A8"/></top><bottom style="thin"><color rgb="FFB8B8A8"/></bottom><diagonal/></border>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<numFmts count="2"><numFmt numFmtId="164" formatCode="&quot;Rp&quot;#,##0"/><numFmt numFmtId="165" formatCode="dd-mm-yyyy"/></numFmts>'
            .'<fonts count="5">'
            .'<font><sz val="11"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="14"/><color rgb="FF1F5F3A"/><name val="Calibri"/></font>'
            .'<font><i/><sz val="10"/><color rgb="FF666666"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="11"/><name val="Calibri"/></font>'
            .'</fonts>'
            .'<fills count="4"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FF1F5F3A"/><bgColor indexed="64"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFE8EFD8"/><bgColor indexed="64"/></patternFill></fill></fills>'
            .'<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border>'.$b.'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="12">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'                                                                                                  // 0 default
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'                                                                                    // 1 judul
            .'<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>'                                                                                    // 2 meta
            .'<xf numFmtId="0" fontId="3" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>' // 3 header
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'                    // 4 teks
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="top"/></xf>'              // 5 angka
            .'<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="top"/></xf>' // 6 uang
            .'<xf numFmtId="165" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top"/></xf>' // 7 tanggal
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top"/></xf>'             // 8 tengah
            .'<xf numFmtId="0" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right"/></xf>'  // 9 total label
            .'<xf numFmtId="164" fontId="4" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right"/></xf>' // 10 total uang
            .'<xf numFmtId="0" fontId="4" fillId="0" borderId="0" xfId="0" applyFont="1"/>'                                                                                    // 11 bold
            .'</cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }
}
