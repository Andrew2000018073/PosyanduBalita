<?php

namespace App\Exports;

use App\Models\Posyandu;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithTitle;


class CatatanBumil implements WithHeadings, WithMapping, WithEvents, WithTitle
{
    protected $bulan;
    protected $idPosyandu;

    public function __construct($idPosyandu)
    {
        $this->bulan = now()->format('F');
        $this->idPosyandu = $idPosyandu;
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
                $posyandu = Posyandu::with(['wus.pus.bayi'])->find($this->idPosyandu);

                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'CATATAN IBU HAMIL, KELAHIRAN, KEMATIAN BAYI, DAN KEMATIAN IBU HAMIL MELAHIRKAN / NIFAS');
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

                $sheet->mergeCells('A8:A9');
                $sheet->setCellValue('A8', 'NO');
                $sheet->mergeCells('B8:C8');
                $sheet->setCellValue('B8', 'NAMA');
                $sheet->setCellValue('B9', 'IBU');
                $sheet->setCellValue('C9', 'BAPAK');
                $sheet->mergeCells('D8:D9');
                $sheet->setCellValue('D8', 'NAMA BAYI');
                $sheet->mergeCells('E8:E9');
                $sheet->setCellValue('E8', 'TANGGAL LAHIR BAYI');
                $sheet->mergeCells('F8:G8');
                $sheet->setCellValue('F8', 'TANGGAL MENINGGAL');
                $sheet->setCellValue('F9', 'IBU');
                $sheet->setCellValue('G9', 'BAYI');
                $sheet->setCellValue('H9', 'Keterangan');

                $sheet->getStyle('A8:H9')->getFont()->setBold(true);
                $sheet->getStyle('A8:H9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A8:H9')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $row = 10;
                foreach ($posyandu->wus as $index => $wus) {
                    $pus = $wus->pus;
                    $bayis = $pus ? $pus->bayi : collect();

                    foreach ($bayis as $bayi) {
                        $sheet->setCellValue("A{$row}", $index + 1);
                        $sheet->setCellValue("B{$row}", $wus->nama);
                        $sheet->setCellValue("C{$row}", $pus->nama_suami ?? '-');
                        $sheet->setCellValue("D{$row}", $bayi->namabalita ?? '-');
                        $sheet->setCellValue("E{$row}", $bayi->tanggal_lahir ?? '-');
                        $sheet->setCellValue("F{$row}", '');
                        $sheet->setCellValue("G{$row}", '');
                        $sheet->setCellValue("H{$row}", $wus->statuswus);
                        $row++;
                    }

                    if ($bayis->isEmpty()) {
                        $sheet->setCellValue("A{$row}", $index + 1);
                        $sheet->setCellValue("B{$row}", $wus->nama);
                        $sheet->setCellValue("C{$row}", $pus->nama_suami ?? '-');
                        $sheet->setCellValue("D{$row}", '-');
                        $sheet->setCellValue("E{$row}", '-');
                        $sheet->setCellValue("F{$row}", '');
                        $sheet->setCellValue("G{$row}", '');
                        $sheet->setCellValue("H{$row}", $wus->statuswus);
                        $row++;
                    }
                }

                $sheet->getStyle("A10:H" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getColumnDimension("B")->setWidth(30);
                $sheet->getColumnDimension("C")->setWidth(30);
                $sheet->getColumnDimension("D")->setWidth(30);
                $sheet->getColumnDimension("E")->setWidth(20);
                $sheet->getColumnDimension("F")->setWidth(20);
                $sheet->getColumnDimension("G")->setWidth(20);
                $sheet->getColumnDimension("H")->setWidth(30);
            },
        ];
    }

    public function title(): string
    {
        return 'Form 1';
    }
}
