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
    protected $tahun;
    public function __construct($idPosyandu,  $tahun)
    {
        $this->bulan = now()->format('F');
        $this->idPosyandu = $idPosyandu;
        $this->tahun = $tahun;
    }


    public function map($posyandu): array
    {
        return [];
    }

    public function headings(): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $posyandu = Posyandu::with([
                    'wus.pus.alatKontrasepsis' => function ($query) {
                        $query->withPivot(['tanggal_pertama_pakai'])
                            ->whereYear(DB::raw("STR_TO_DATE(pivot_kontrasepsi_puses.tanggal_pertama_pakai, '%d-%m-%Y')"), $this->tahun);
                    }
                ])->find($this->idPosyandu);


                $sheet->mergeCells('A1:Q1');
                $sheet->setCellValue('A1', 'REGISTER WUS DAN PUS');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setUnderline(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A3', 'POSYANDU:');
                $sheet->setCellValue('C3', $posyandu->nama_posyandu ?? '-');
                $sheet->setCellValue('A4', 'DESA / KELURAHAN:');
                $sheet->setCellValue('C4', $posyandu->desa ?? '-');
                $sheet->setCellValue('A5', 'PUSKESMAS:');
                $sheet->setCellValue('C5', 'Kelapa Kampit');
                $sheet->setCellValue('A6', 'BULAN:');
                $sheet->setCellValue('C6', $this->bulan);

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

                $sheet->getStyle('A8:Q9')->getFont()->setBold(true);
                $sheet->getStyle('A8:Q9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A8:Q9')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $row = 10;
                foreach ($posyandu->wus as $index => $wus) {
                    $pus = $wus->pus ?? null;
                    $periksaWus = $wus->periksawus ?? collect();

                    $kontrasepsiSedangDipakai = $pus ? $pus->alatKontrasepsis()
                        ->wherePivot('status', 'Sedang dipakai')
                        ->first() : null;

                    $kontrasepsiSebelumnya = $pus ? $pus->alatKontrasepsis()
                        ->wherePivot('status', 'Sudah diganti')
                        ->orderBy('pivot_tanggal_berhenti_pakai', 'desc')
                        ->first() : null;

                    $sheet->setCellValue("A{$row}", $index + 1);
                    $sheet->setCellValue("B{$row}", $wus->nama ?? '-');
                    $sheet->setCellValue("C{$row}", $wus->getUmur() ?? '-');
                    $sheet->setCellValue("D{$row}", $pus->nama_suami ?? '-');
                    $sheet->setCellValue("E{$row}", '-');
                    $sheet->setCellValue("F{$row}", '');
                    $sheet->setCellValue("G{$row}", $pus->jumlah_anak_hidup ?? '-');
                    $sheet->setCellValue("H{$row}", '');
                    $sheet->setCellValue("I{$row}", $wus->lila_wus ?? '-');

                    for ($i = 1; $i <= 5; $i++) {
                        $imunisasi = $periksaWus->where('imunisasi_kehamilans_kuartal', $i)->first();
                        $col = chr(76 + $i);
                        $sheet->setCellValue("{$col}{$row}", $imunisasi ? $imunisasi->Kegiatanposyandu->tgl_kegiatan ?? '-' : '-');
                    }

                    $sheet->setCellValue("O{$row}", $kontrasepsiSedangDipakai->nama ?? '-');

                    $sheet->setCellValue("P{$row}", $kontrasepsiSebelumnya->pivot->tanggal_berhenti_pakai ?? '-');

                    $sheet->setCellValue("Q{$row}", $kontrasepsiSebelumnya->nama ?? '-');

                    $row++;
                }



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
        return 'Form 3';
    }
}
