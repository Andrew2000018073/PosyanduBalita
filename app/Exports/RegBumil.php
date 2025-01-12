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


class RegBumil implements WithHeadings, WithMapping, WithEvents, WithTitle
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
                    'wus.periksawus.kegiatanposyandu' => function ($query) {
                        $query->whereYear(DB::raw("STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, '%d-%m-%Y')"), $this->tahun);
                    },
                    'wus.Kehamilan' => function ($query) {
                        $query->whereYear(DB::raw("STR_TO_DATE(kehamilans.tanggal_daftar, '%d-%m-%Y')"), $this->tahun);
                    }
                ])->find($this->idPosyandu);




                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'REGISTER IBU HAMIL DAN NIFAS');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setUnderline(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A3', 'POSYANDU:');
                $sheet->setCellValue('C3', $posyandu->nama_posyandu);
                $sheet->setCellValue('A4', 'DESA / KELURAHAN:');
                $sheet->setCellValue('C4', $posyandu->desa);
                $sheet->setCellValue('A5', 'PUSKESMAS:');
                $sheet->setCellValue('C5', 'Kelapa Kampit');
                $sheet->setCellValue('A6', 'BULAN:');
                $sheet->setCellValue('C6', $this->bulan);

                $sheet->mergeCells('A8:A10');
                $sheet->setCellValue('A8', 'NO');
                $sheet->mergeCells('B8:B10');
                $sheet->setCellValue('B8', 'NAMA IBU');
                $sheet->mergeCells('C8:C10');
                $sheet->setCellValue('C8', 'UMUR');
                $sheet->mergeCells('D8:D10');
                $sheet->setCellValue('D8', 'KELOMPOK DASAWISMA');
                $sheet->mergeCells('E8:F8');
                $sheet->setCellValue('E8', 'PENDAFTARAN');
                $sheet->mergeCells('E9:E10');
                $sheet->setCellValue('E9', 'TANGGAL');
                $sheet->mergeCells('F9:F10');
                $sheet->setCellValue('F9', 'UMUR KEHAMILAN');
                $sheet->mergeCells('G8:G10');
                $sheet->setCellValue('G8', 'HAMIL KE');
                $sheet->mergeCells('H8:H10');
                $sheet->setCellValue('H8', 'LILA');
                $sheet->mergeCells('I8:I10');
                $sheet->setCellValue('I8', 'PMT PEMULIHAN');
                $sheet->mergeCells('J8:J10');
                $sheet->setCellValue('J8', 'HASIL TIMBANGAN');
                $sheet->setCellValue('C9', 'BAPAK');
                $sheet->mergeCells('K8:M8');
                $sheet->setCellValue('K8', 'TABLET TAMBAH DARAH');
                $sheet->mergeCells('K9:M9');
                $sheet->setCellValue('K10', 'I');
                $sheet->setCellValue('L10', 'II');
                $sheet->setCellValue('M10', 'III');
                $sheet->mergeCells('N8:R8');
                $sheet->setCellValue('N8', 'PEMBERIAN IMUNISASI');
                $sheet->mergeCells('N9:N10');
                $sheet->mergeCells('O9:O10');
                $sheet->mergeCells('P9:P10');
                $sheet->mergeCells('Q9:Q10');
                $sheet->mergeCells('R9:R10');
                $sheet->setCellValue('N9', 'I');
                $sheet->setCellValue('O9', 'II');
                $sheet->setCellValue('P9', 'III');
                $sheet->setCellValue('Q9', 'IV');
                $sheet->setCellValue('R9', 'V');
                $sheet->mergeCells('S8:S10');
                $sheet->mergeCells('T8:T10');
                $sheet->setCellValue('S8', 'VITAMIN');
                $sheet->setCellValue('T8', 'CATATAN');


                $sheet->getStyle('A8:T10')->getFont()->setBold(true);
                $sheet->getStyle('A8:T10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A8:T10')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A8:T10')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $row = 11;
                $filteredWus = $posyandu->wus->filter(function ($wus) {
                    return $wus->periksawus?->contains(function ($periksa) {
                        return in_array($periksa->statusperiksa, ['Hamil', 'Nifas']);
                    });
                });

                foreach ($filteredWus as $index => $wus) {
                    $periksaWus = $wus->periksawus ?? collect();

                    $filteredPeriksaWus = $periksaWus->filter(function ($periksa) {
                        return in_array($periksa->statusperiksa, ['Hamil', 'Nifas']);
                    });

                    $kehamilanTerbaru = $wus->kehamilan()->latest('tanggal_daftar')->first();

                    $sheet->setCellValue("A{$row}", $index + 1);
                    $sheet->setCellValue("B{$row}", $wus->nama ?? '-');
                    $sheet->setCellValue("C{$row}", $wus->getUmur() ?? '-');

                    $sheet->setCellValue("D{$row}", $wus->kelompok_dasawisma ?? '-');

                    $sheet->setCellValue("E{$row}", $kehamilanTerbaru->tanggal_daftar ?? '-');
                    if ($kehamilanTerbaru) {
                        $umurKehamilan = $kehamilanTerbaru->tanggal_awal_hamil
                            ? now()->diffInMonths($kehamilanTerbaru->tanggal_awal_hamil) . ' bulan'
                            : '-';
                    } else {
                        $umurKehamilan = '-';
                    }

                    $sheet->setCellValue("F{$row}", $umurKehamilan);
                    $sheet->setCellValue("G{$row}", $kehamilanTerbaru->kehamilan_ke ?? '-');

                    $lilaPeriksa = $filteredPeriksaWus->last()?->lila_periksa;
                    $sheet->setCellValue("H{$row}", $lilaPeriksa ?? '-');

                    $tablet1 = $filteredPeriksaWus->where('tablettambah_darahs_kuartal', '1')->first();
                    $tablet2 = $filteredPeriksaWus->where('tablettambah_darahs_kuartal', '2')->first();
                    $tablet3 = $filteredPeriksaWus->where('tablettambah_darahs_kuartal', '3')->first();

                    $sheet->setCellValue("K{$row}", $tablet1 ? $tablet1->Kegiatanposyandu->tgl_kegiatan ?? '-' : '-');
                    $sheet->setCellValue("L{$row}", $tablet2 ? $tablet2->Kegiatanposyandu->tgl_kegiatan ?? '-' : '-');
                    $sheet->setCellValue("M{$row}", $tablet3 ? $tablet3->Kegiatanposyandu->tgl_kegiatan ?? '-' : '-');

                    for ($i = 1; $i <= 5; $i++) {
                        $imunisasi = $filteredPeriksaWus->where('imunisasi_kehamilans_kuartal', $i)->first();
                        $col = chr(76 + $i);
                        $sheet->setCellValue("{$col}{$row}", $imunisasi ? $imunisasi->Kegiatanposyandu->tgl_kegiatan ?? '-' : '-');
                    }

                    $vitaminTerbaru = $filteredPeriksaWus->last()?->vitamin_kuartal;
                    $sheet->setCellValue("S{$row}", $vitaminTerbaru ?? '-');

                    $sheet->setCellValue("T{$row}", '');

                    $row++;
                }



                $sheet->getStyle("A10:T" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getColumnDimension("B")->setWidth(30);
                $sheet->getColumnDimension("C")->setWidth(20);
                $sheet->getColumnDimension("D")->setWidth(20);
                $sheet->getColumnDimension("E")->setWidth(20);
                $sheet->getColumnDimension("F")->setWidth(20);
                $sheet->getColumnDimension("G")->setWidth(20);
                $sheet->getColumnDimension("H")->setWidth(20);
                $sheet->getColumnDimension("I")->setWidth(20);
                $sheet->getColumnDimension("J")->setWidth(20);
                $sheet->getColumnDimension("K")->setWidth(20);
                $sheet->getColumnDimension("L")->setWidth(20);
                $sheet->getColumnDimension("M")->setWidth(20);
                $sheet->getColumnDimension("N")->setWidth(20);
                $sheet->getColumnDimension("O")->setWidth(20);
                $sheet->getColumnDimension("P")->setWidth(20);
                $sheet->getColumnDimension("Q")->setWidth(20);
                $sheet->getColumnDimension("R")->setWidth(20);
                $sheet->getColumnDimension("S")->setWidth(20);
                $sheet->getColumnDimension("T")->setWidth(20);
            },
        ];
    }

    public function title(): string
    {
        return 'Form 4';
    }
}
