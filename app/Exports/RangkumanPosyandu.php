<?php

namespace App\Exports;

use App\Models\Posyandu;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RangkumanPosyandu implements WithHeadings, WithMapping, WithEvents, WithTitle
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
                $posyandu = Posyandu::with(['wus.pus.bayi', 'wus.periksawus.Kegiatanposyandu' => function ($query) {
                    $query->whereYear(DB::raw("STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, '%d-%m-%Y')"), $this->tahun);
                }])->find($this->idPosyandu);

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
                $sheet->mergeCells('B8:B9');
                $sheet->setCellValue('B8', 'Bulan');
                $sheet->mergeCells('C8:D8');
                $sheet->setCellValue('C8', 'BAYI 0-12 BULAN');
                $sheet->mergeCells('E8:F8');
                $sheet->setCellValue('E8', 'BAYI 1-5 TAHUN');
                $sheet->setCellValue('C9', 'LK');
                $sheet->setCellValue('D9', 'PR');
                $sheet->setCellValue('E9', 'LK');
                $sheet->setCellValue('F9', 'PR');
                $sheet->mergeCells('G8:G9');
                $sheet->setCellValue('G8', 'WUS');
                $sheet->mergeCells('H8:J8');
                $sheet->setCellValue('H8', 'IBU');
                $sheet->setCellValue('H9', 'PUS');
                $sheet->setCellValue('I9', 'HAMIL');
                $sheet->setCellValue('J9', 'MENYUSUI');
                $sheet->mergeCells('K8:L8');
                $sheet->setCellValue('K8', 'JUMLAH BAYI');
                $sheet->setCellValue('K9', 'LAHIR');
                $sheet->setCellValue('L9', 'WAFAT');
                $sheet->setCellValue('M8', 'JUMLAH KEMATIAN');
                $sheet->setCellValue('M9', 'IBU HAMIL, MELAHIRKAN, NIFAS');
                $sheet->mergeCells('N8:P8');
                $sheet->setCellValue('N8', 'JUMLAH PETUGAS HADIR');
                $sheet->setCellValue('N9', 'KADER POSYANDU');
                $sheet->setCellValue('O9', 'PLKB');
                $sheet->setCellValue('P9', 'MEDIS DAN PARAMEDIS');
                $sheet->mergeCells('Q8:Q9');
                $sheet->setCellValue('Q8', 'KET');

                $sheet->getStyle('A8:Q9')->getFont()->setBold(true);
                $sheet->getStyle('A8:Q9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A8:Q9')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $bulanSekarang = date('n');
                $row = 10;

                for ($bulan = 1; $bulan <= $bulanSekarang; $bulan++) {
                    $sheet->setCellValue("A{$row}", $bulan);
                    $sheet->setCellValue("B{$row}", date("F", mktime(0, 0, 0, $bulan, 10)));

                    $kegiatanBulan = $posyandu->wus->pluck('periksawus')
                        ->flatten()
                        ->filter(function ($periksa) use ($bulan) {
                            return date('m', strtotime($periksa->Kegiatanposyandu->tgl_kegiatan)) == sprintf('%02d', $bulan);
                        });

                    $wusPus = $kegiatanBulan->where('statusperiksa', 'Tidak hamil & Tidak menyusui')
                        ->merge($kegiatanBulan->where('statusperiksa', 'Nifas'))->count();
                    $wusHamil = $kegiatanBulan->where('statusperiksa', 'Hamil')->count();
                    $wusMenyusui = $kegiatanBulan->where('statusperiksa', 'Tidak hamil & Menyusui')->count();
                    $totalWus = $wusPus + $wusHamil + $wusMenyusui + $kegiatanBulan->where('statusperiksa', 'Tidak menikah')->count();

                    $sheet->setCellValue("G{$row}", $totalWus);
                    $sheet->setCellValue("H{$row}", $wusPus);
                    $sheet->setCellValue("I{$row}", $wusHamil);
                    $sheet->setCellValue("J{$row}", $wusMenyusui);

                    $bayiBulan = $posyandu->wus->pluck('pus.bayi')
                        ->flatten()
                        ->filter(function ($bayi) use ($bulan) {
                            return $bayi && isset($bayi->tanggal_lahir)
                                && date('m', strtotime($bayi->tanggal_lahir)) <= sprintf('%02d', $bulan);
                        });

                    $bayiBulan->transform(function ($bayi) {
                        $tanggalLahir = Carbon::parse($bayi->tanggal_lahir);
                        $umurDalamBulan = $tanggalLahir->diffInMonths(Carbon::now());
                        $bayi->umurDalamBulan = $umurDalamBulan;
                        return $bayi;
                    });

                    $bayi1_5LK_awal = $bayiBulan->filter(function ($bayi) {
                        return $bayi->jenis_kelamin == 'Laki-laki' && $bayi->umurDalamBulan > 12 && $bayi->umurDalamBulan <= 60;
                    })->count();

                    $bayi1_5PR_awal = $bayiBulan->filter(function ($bayi) {
                        return $bayi->jenis_kelamin == 'Perempuan' && $bayi->umurDalamBulan > 12 && $bayi->umurDalamBulan <= 60;
                    })->count();

                    $bayi0_12LK = $bayiBulan->filter(function ($bayi) {
                        return $bayi->jenis_kelamin == 'Laki-laki' && $bayi->umurDalamBulan <= 12;
                    })->count();

                    $bayi0_12PR = $bayiBulan->filter(function ($bayi) {
                        return $bayi->jenis_kelamin == 'Perempuan' && $bayi->umurDalamBulan <= 12;
                    })->count();

                    $bayi1_5LK = $bayiBulan->filter(function ($bayi) {
                        return $bayi->jenis_kelamin == 'Laki-laki' && $bayi->umurDalamBulan > 12 && $bayi->umurDalamBulan <= 60;
                    })->count();

                    $bayi1_5PR = $bayiBulan->filter(function ($bayi) {
                        return $bayi->jenis_kelamin == 'Perempuan' && $bayi->umurDalamBulan > 12 && $bayi->umurDalamBulan <= 60;
                    })->count();

                    if ($bayi1_5LK < $bayi1_5LK_awal) {
                        $selisih = $bayi1_5LK_awal - $bayi1_5LK;
                        $bayi0_12LK += $selisih;
                    }

                    if ($bayi1_5PR < $bayi1_5PR_awal) {
                        $selisih = $bayi1_5PR_awal - $bayi1_5PR;
                        $bayi0_12PR += $selisih;
                    }

                    $sheet->setCellValue("C{$row}", $bayi0_12LK);
                    $sheet->setCellValue("D{$row}", $bayi0_12PR);
                    $sheet->setCellValue("E{$row}", $bayi1_5LK);
                    $sheet->setCellValue("F{$row}", $bayi1_5PR);

                    $sheet->setCellValue("K{$row}", '');
                    $sheet->setCellValue("L{$row}", '');
                    $sheet->setCellValue("M{$row}", '');
                    $sheet->setCellValue("N{$row}", '');
                    $sheet->setCellValue("O{$row}", '');
                    $sheet->setCellValue("P{$row}", '');
                    $sheet->setCellValue("Q{$row}", '');

                    $row++;
                }


                $sheet->getStyle("A10:Q" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
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
        return 'Form 5';
    }
}
