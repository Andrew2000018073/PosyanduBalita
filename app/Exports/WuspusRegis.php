<?php

namespace App\Exports;

use App\Models\Posyandu;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class WuspusRegis implements WithHeadings, WithMapping, WithEvents, WithTitle
{
    protected $bulan;
    protected $idPosyandu;
    protected $tahun; // Deklarasi properti $tahun
    public function __construct($idPosyandu,  $tahun)
    {
        $this->bulan = now()->format('F'); // Bulan saat ini
        $this->idPosyandu = $idPosyandu; // ID Posyandu yang dipilih
        $this->tahun = $tahun;
    }


    /**
     * Fungsi map bisa dikosongkan karena data akan diproses di registerEvents.
     */
    public function map($posyandu): array
    {
        return [];
    }

    /**
     * Fungsi headings bisa dikosongkan karena pemformatan ditangani di registerEvents.
     */
    public function headings(): array
    {
        return [];
    }

    /**
     * Mendaftarkan event untuk pemformatan sheet.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $posyandu = Posyandu::with([
                    'wus.pus.alatKontrasepsis' => function ($query) {
                        $query->withPivot(['tanggal_pertama_pakai'])
                            ->whereYear(DB::raw("STR_TO_DATE(pivot_kontrasepsi_puses.tanggal_pertama_pakai, '%d-%m-%Y')"), $this->tahun); // Filter berdasarkan tahun
                    }
                ])->find($this->idPosyandu);


                // Judul Utama
                $sheet->mergeCells('A1:Q1');
                $sheet->setCellValue('A1', 'REGISTER WUS DAN PUS');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setUnderline(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Subjudul
                $sheet->setCellValue('A3', 'POSYANDU:');
                $sheet->setCellValue('C3', $posyandu->nama_posyandu ?? '-');
                $sheet->setCellValue('A4', 'DESA / KELURAHAN:');
                $sheet->setCellValue('C4', $posyandu->desa ?? '-');
                $sheet->setCellValue('A5', 'PUSKESMAS:');
                $sheet->setCellValue('C5', 'Kelapa Kampit');
                $sheet->setCellValue('A6', 'BULAN:');
                $sheet->setCellValue('C6', $this->bulan);

                // Heading Tabel
                $sheet->mergeCells('A8:A9');
                $sheet->setCellValue('A8', 'NO');
                $sheet->mergeCells('B8:B9');
                $sheet->setCellValue('B8', 'NAMA WUS');
                $sheet->mergeCells('C8:C9');
                $sheet->setCellValue('C8', 'Umur');
                $sheet->mergeCells('D8:D9');
                $sheet->setCellValue('D8', 'Suami');
                $sheet->mergeCells('E8:E9');
                $sheet->setCellValue('E8', 'Tahapan KS');
                $sheet->mergeCells('F8:F9');
                $sheet->setCellValue('F8', 'Kelompok Dasa Wisma');
                $sheet->mergeCells('G8:H8');
                $sheet->setCellValue('G8', 'Jumlah Anak');
                $sheet->setCellValue('G9', 'Yang Hidup');
                $sheet->setCellValue('H9', 'Meninggal Pada Umur');
                $sheet->mergeCells('I8:I9');
                $sheet->setCellValue('I8', 'PENGUKURAN LILA ≤ ATAU 23,5 cm');
                $sheet->mergeCells('J8:N8');
                $sheet->setCellValue('J8', 'Pemberian Imunisasi TT');
                $sheet->setCellValue('J9', 'I');
                $sheet->setCellValue('K9', 'II');
                $sheet->setCellValue('L9', 'III');
                $sheet->setCellValue('M9', 'IV');
                $sheet->setCellValue('N9', 'V');
                $sheet->mergeCells('O8:O9');
                $sheet->setCellValue('O8', 'JENIS KONTRASEPSI YANG DIPAKAI');
                $sheet->mergeCells('P8:Q8');
                $sheet->setCellValue('P8', 'PENGGANTIAN');
                $sheet->setCellValue('P9', 'TANGGAL / BULAN');
                $sheet->setCellValue('Q9', 'JENIS KONTRASEPSI');

                // Style Heading
                $sheet->getStyle('A8:Q9')->getFont()->setBold(true);
                $sheet->getStyle('A8:Q9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A8:Q9')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Data Tabel
                $row = 10;
                foreach ($posyandu->wus as $index => $wus) {
                    $pus = $wus->pus ?? null;
                    $periksaWus = $wus->periksawus ?? collect();

                    // Ambil alat kontrasepsi yang sedang dipakai
                    $kontrasepsiSedangDipakai = $pus ? $pus->alatKontrasepsis()
                        ->wherePivot('status', 'Sedang dipakai')
                        ->first() : null;

                    // Ambil alat kontrasepsi terakhir yang statusnya "Sudah diganti"
                    $kontrasepsiSebelumnya = $pus ? $pus->alatKontrasepsis()
                        ->wherePivot('status', 'Sudah diganti')
                        ->orderBy('pivot_tanggal_berhenti_pakai', 'desc')
                        ->first() : null;

                    // Set data hanya sekali tanpa perulangan alat kontrasepsi
                    $sheet->setCellValue("A{$row}", $index + 1); // Nomor
                    $sheet->setCellValue("B{$row}", $wus->nama ?? '-'); // Nama WUS
                    $sheet->setCellValue("C{$row}", $wus->getUmur() ?? '-'); // Umur
                    $sheet->setCellValue("D{$row}", $pus->nama_suami ?? '-'); // Nama Suami
                    $sheet->setCellValue("E{$row}", '-'); // Kolom kosong
                    $sheet->setCellValue("F{$row}", '');
                    $sheet->setCellValue("G{$row}", $pus->jumlah_anak_hidup ?? '-'); // Jumlah Anak Hidup
                    $sheet->setCellValue("H{$row}", '');
                    $sheet->setCellValue("I{$row}", $wus->lila_wus ?? '-'); // LILA WUS

                    // PEMBERIAN IMUNISASI (I - V)
                    for ($i = 1; $i <= 5; $i++) {
                        $imunisasi = $periksaWus->where('imunisasi_kehamilans_kuartal', $i)->first();
                        $col = chr(76 + $i); // Kolom mulai dari N
                        $sheet->setCellValue("{$col}{$row}", $imunisasi ? $imunisasi->Kegiatanposyandu->tgl_kegiatan ?? '-' : '-');
                    }

                    // Isi alat kontrasepsi yang sedang dipakai
                    $sheet->setCellValue("O{$row}", $kontrasepsiSedangDipakai->nama ?? '-');

                    // Isi tanggal ganti dari alat sebelumnya
                    $sheet->setCellValue("P{$row}", $kontrasepsiSebelumnya->pivot->tanggal_berhenti_pakai ?? '-');

                    // Isi jenis alat kontrasepsi sebelumnya
                    $sheet->setCellValue("Q{$row}", $kontrasepsiSebelumnya->nama ?? '-');

                    $row++; // Pindah ke baris berikutnya
                }



                // Border untuk data
                $sheet->getStyle("A10:Q" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getColumnDimension("B")->setWidth(30);
                $sheet->getColumnDimension("C")->setWidth(20);
                $sheet->getColumnDimension("D")->setWidth(30);
                $sheet->getColumnDimension("E")->setWidth(20);
                $sheet->getColumnDimension("F")->setWidth(20);
                $sheet->getColumnDimension("G")->setWidth(20);
                $sheet->getColumnDimension("H")->setWidth(20);
                $sheet->getColumnDimension("I")->setWidth(30);
                $sheet->getColumnDimension("O")->setWidth(30);
                $sheet->getColumnDimension("P")->setWidth(20);
                $sheet->getColumnDimension("Q")->setWidth(20);
            },
        ];
    }



    public function title(): string
    {
        return 'Form 3'; // Nama worksheet untuk Sheet 1
    }
}
