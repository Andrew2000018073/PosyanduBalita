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
                $posyandu = Posyandu::with(['wus.pus.bayi', 'wus.periksawus.Kegiatanposyandu' => function ($query) {
                    $query->whereYear(DB::raw("STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, '%d-%m-%Y')"), $this->tahun);
                }])->find($this->idPosyandu);
                // dd($posyandu->toArray());

                // Subjudul
                $sheet->setCellValue('A3', 'POSYANDU:');
                $sheet->setCellValue('C3', $posyandu->nama_posyandu);
                $sheet->setCellValue('A4', 'DESA / KELURAHAN:');
                $sheet->setCellValue('C4', $posyandu->desa);
                $sheet->setCellValue('A5', 'PUSKESMAS:');
                $sheet->setCellValue('C5', 'Kelapa Kampit');
                $sheet->setCellValue('A6', 'BULAN:');
                $sheet->setCellValue('C6', $this->bulan);

                // Heading Tabel
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
                $sheet->setCellValue('N9', 'KADER POSYANDU'); //Kosongkan
                $sheet->setCellValue('O9', 'PLKB'); //Kosongkan
                $sheet->setCellValue('P9', 'MEDIS DAN PARAMEDIS'); //Kosongkan
                $sheet->mergeCells('Q8:Q9');
                $sheet->setCellValue('Q8', 'KET'); //Kosongkan

                // Style Heading
                $sheet->getStyle('A8:Q9')->getFont()->setBold(true);
                $sheet->getStyle('A8:Q9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A8:Q9')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Data Tabel
                $bulanSekarang = date('n'); // Ambil bulan sekarang (1-12)
                $row = 10; // Baris awal untuk data

                for ($bulan = 1; $bulan <= $bulanSekarang; $bulan++) {
                    $sheet->setCellValue("A{$row}", $bulan); // NO
                    $sheet->setCellValue("B{$row}", date("F", mktime(0, 0, 0, $bulan, 10))); // Bulan (nama bulan)

                    // Filter kegiatan posyandu berdasarkan bulan
                    $kegiatanBulan = $posyandu->wus->pluck('periksawus')
                        ->flatten()
                        ->filter(function ($periksa) use ($bulan) {
                            return date('m', strtotime($periksa->Kegiatanposyandu->tgl_kegiatan)) == sprintf('%02d', $bulan);
                        });

                    // WUS
                    $wusPus = $kegiatanBulan->where('statusperiksa', 'Tidak hamil & Tidak menyusui')
                        ->merge($kegiatanBulan->where('statusperiksa', 'Nifas'))->count();
                    $wusHamil = $kegiatanBulan->where('statusperiksa', 'Hamil')->count();
                    $wusMenyusui = $kegiatanBulan->where('statusperiksa', 'Tidak hamil & Menyusui')->count();
                    $totalWus = $wusPus + $wusHamil + $wusMenyusui + $kegiatanBulan->where('statusperiksa', 'Tidak menikah')->count();

                    $sheet->setCellValue("G{$row}", $totalWus); // WUS
                    $sheet->setCellValue("H{$row}", $wusPus); // PUS
                    $sheet->setCellValue("I{$row}", $wusHamil); // HAMIL
                    $sheet->setCellValue("J{$row}", $wusMenyusui); // MENYUSUI

                    // Ambil data bayi berdasarkan bulan
                    $bayiBulan = $posyandu->wus->pluck('pus.bayi')
                        ->flatten()
                        ->filter(function ($bayi) use ($bulan) {
                            return $bayi && isset($bayi->tanggal_lahir)
                                && date('m', strtotime($bayi->tanggal_lahir)) <= sprintf('%02d', $bulan);
                        });

                    // Hitung umur dalam bulan dan tambahkan ke objek bayi
                    $bayiBulan->transform(function ($bayi) {
                        $tanggalLahir = Carbon::parse($bayi->tanggal_lahir);
                        $umurDalamBulan = $tanggalLahir->diffInMonths(Carbon::now());
                        $bayi->umurDalamBulan = $umurDalamBulan;
                        return $bayi;
                    });

                    // Simpan nilai awal bayi 1-5 tahun
                    $bayi1_5LK_awal = $bayiBulan->filter(function ($bayi) {
                        return $bayi->jenis_kelamin == 'Laki-laki' && $bayi->umurDalamBulan > 12 && $bayi->umurDalamBulan <= 60;
                    })->count();

                    $bayi1_5PR_awal = $bayiBulan->filter(function ($bayi) {
                        return $bayi->jenis_kelamin == 'Perempuan' && $bayi->umurDalamBulan > 12 && $bayi->umurDalamBulan <= 60;
                    })->count();

                    // Hitung bayi 0-12 bulan
                    $bayi0_12LK = $bayiBulan->filter(function ($bayi) {
                        return $bayi->jenis_kelamin == 'Laki-laki' && $bayi->umurDalamBulan <= 12;
                    })->count();

                    $bayi0_12PR = $bayiBulan->filter(function ($bayi) {
                        return $bayi->jenis_kelamin == 'Perempuan' && $bayi->umurDalamBulan <= 12;
                    })->count();

                    // Hitung ulang bayi 1-5 tahun (setelah validasi)
                    $bayi1_5LK = $bayiBulan->filter(function ($bayi) {
                        return $bayi->jenis_kelamin == 'Laki-laki' && $bayi->umurDalamBulan > 12 && $bayi->umurDalamBulan <= 60;
                    })->count();

                    $bayi1_5PR = $bayiBulan->filter(function ($bayi) {
                        return $bayi->jenis_kelamin == 'Perempuan' && $bayi->umurDalamBulan > 12 && $bayi->umurDalamBulan <= 60;
                    })->count();

                    // Penyesuaian jika terjadi pengurangan
                    if ($bayi1_5LK < $bayi1_5LK_awal) {
                        $selisih = $bayi1_5LK_awal - $bayi1_5LK;
                        $bayi0_12LK += $selisih;
                    }

                    if ($bayi1_5PR < $bayi1_5PR_awal) {
                        $selisih = $bayi1_5PR_awal - $bayi1_5PR;
                        $bayi0_12PR += $selisih;
                    }

                    $sheet->setCellValue("C{$row}", $bayi0_12LK); // LK 0-12 BULAN
                    $sheet->setCellValue("D{$row}", $bayi0_12PR); // PR 0-12 BULAN
                    $sheet->setCellValue("E{$row}", $bayi1_5LK); // LK 1-5 TAHUN
                    $sheet->setCellValue("F{$row}", $bayi1_5PR); // PR 1-5 TAHUN

                    $sheet->setCellValue("K{$row}", ''); // JUMLAH BAYI LAHIR (Sementara dikosongkan)
                    $sheet->setCellValue("L{$row}", ''); // JUMLAH BAYI WAFAT (Sementara dikosongkan)
                    $sheet->setCellValue("M{$row}", ''); // JUMLAH KEMATIAN IBU (Kosong)
                    $sheet->setCellValue("N{$row}", ''); // KADER POSYANDU (Kosong)
                    $sheet->setCellValue("O{$row}", ''); // PLKB (Kosong)
                    $sheet->setCellValue("P{$row}", ''); // MEDIS DAN PARAMEDIS (Kosong)
                    $sheet->setCellValue("Q{$row}", ''); // KETERANGAN (Kosong)

                    $row++;
                }


                // Border untuk data
                $sheet->getStyle("A10:Q" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getColumnDimension("B")->setWidth(30);
                $sheet->getColumnDimension("C")->setWidth(30);
                $sheet->getColumnDimension("D")->setWidth(30);
                $sheet->getColumnDimension("E")->setWidth(20);
                $sheet->getColumnDimension("F")->setWidth(20);
                $sheet->getColumnDimension("G")->setWidth(20);
                $sheet->getColumnDimension("H")->setWidth(30);
                // dd($bayiBulan);
                // dd($bulan);
            },
        ];
    }





    public function title(): string
    {
        return 'Form 5'; // Nama worksheet untuk Sheet 1
    }
}
