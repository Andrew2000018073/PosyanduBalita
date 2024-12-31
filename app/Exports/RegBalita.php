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


class RegBalita implements WithHeadings, WithMapping, WithEvents, WithTitle
{
    protected $bulan;
    protected $idPosyandu;
    protected $tahun; // Deklarasi properti $tahun

    // Fungsi untuk mencari tanggal imunisasi berdasarkan nama imunisasi
    private function getTanggalImunisasi($periksabalita, $namaImunisasi)
    {
        foreach ($periksabalita as $periksa) {
            if ($periksa->imunisasi->contains('nama', $namaImunisasi)) {
                return $periksa->Kegiatanposyandu->tgl_kegiatan ?? '-';
            }
        }
        return '-';
    }

    private function getTanggalVitamin($periksabalita, $value)
    {
        foreach ($periksabalita as $periksa) {
            if ($periksa->vitamin_kuartal == $value) {
                return $periksa->Kegiatanposyandu->tgl_kegiatan ?? '-';
            }
        }
        return '-';
    }

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
                    'wus.pus.bayi.PeriksaBalita.Kegiatanposyandu' => function ($query) {
                        $query->whereYear(DB::raw("STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, '%d-%m-%Y')"), $this->tahun); // Filter berdasarkan tgl_kegiatan
                    },
                    'wus.pus.bayi.PeriksaBalita.imunisasi' => function ($query) {
                        $query->whereYear('imunisasi_balitas.created_at', $this->tahun); // Tetap menggunakan created_at untuk imunisasi
                    },
                ])->find($this->idPosyandu);

                // dd($posyandu->toArray());


                // Judul Utama
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'REGISTER BAYI DAN BALITA');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setUnderline(true);

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
                $sheet->mergeCells('A8:A10');
                $sheet->setCellValue('A8', 'NO');
                $sheet->mergeCells('B8:B10');
                $sheet->setCellValue('B8', 'Nama Balita/Bayi');
                $sheet->mergeCells('C8:C10');
                $sheet->setCellValue('C8', 'Tanggal Lahir');
                $sheet->mergeCells('D8:D10');
                $sheet->setCellValue('D8', 'Berat Badan Lahir');
                $sheet->mergeCells('E8:F8');
                $sheet->setCellValue('E8', 'NAMA');
                $sheet->mergeCells('E9:E10');
                $sheet->setCellValue('E9', 'Ayah');
                $sheet->mergeCells('F9:F10');
                $sheet->setCellValue('F9', 'Ibu');
                $sheet->mergeCells('G8:G10');
                $sheet->setCellValue('G8', 'Kelompok Dasawisma');

                $sheet->mergeCells('H8:S8');
                $sheet->setCellValue('H8', 'Hasil Penimbangan KG');
                $sheet->mergeCells('H9:H10');
                $sheet->setCellValue('H9', 'Jan');
                $sheet->mergeCells('I9:I10');
                $sheet->setCellValue('I9', 'Feb');
                $sheet->mergeCells('J9:J10');
                $sheet->setCellValue('J9', 'Mar');
                $sheet->mergeCells('K9:K10');
                $sheet->setCellValue('K9', 'Apr');
                $sheet->mergeCells('L9:L10');
                $sheet->setCellValue('L9', 'Mei');
                $sheet->mergeCells('M9:M10');
                $sheet->setCellValue('M9', 'Jun');
                $sheet->mergeCells('N9:N10');
                $sheet->setCellValue('N9', 'Jul');
                $sheet->mergeCells('O9:O10');
                $sheet->setCellValue('O9', 'Agu');
                $sheet->mergeCells('P9:P10');
                $sheet->setCellValue('P9', 'Sep');
                $sheet->mergeCells('Q9:Q10');
                $sheet->setCellValue('Q9', 'Okt');
                $sheet->mergeCells('R9:R10');
                $sheet->setCellValue('R9', 'Nov');
                $sheet->mergeCells('S9:S10');
                $sheet->setCellValue('S9', 'Des');

                $sheet->mergeCells('T8:Y8');
                $sheet->setCellValue('T8', 'Pemberian Asi');
                $sheet->mergeCells('T9:T10');
                $sheet->setCellValue('T9', 'E1');
                $sheet->mergeCells('U9:U10');
                $sheet->setCellValue('U9', 'E2');
                $sheet->mergeCells('V9:V10');
                $sheet->setCellValue('V9', 'E3');
                $sheet->mergeCells('W9:W10');
                $sheet->setCellValue('W9', 'E4');
                $sheet->mergeCells('X9:X10');
                $sheet->setCellValue('X9', 'E5');
                $sheet->mergeCells('Y9:Y10');
                $sheet->setCellValue('Y9', 'E6');

                $sheet->mergeCells('Z8:AA8');
                $sheet->setCellValue('Z8', 'Pelayanan');
                $sheet->mergeCells('Z9:AA9');
                $sheet->setCellValue('Z9', 'Vitamin A');
                $sheet->setCellValue('Z10', 'bl');
                $sheet->setCellValue('AA10', 'bl');

                $sheet->mergeCells('AB8:AP8');
                $sheet->setCellValue('AB8', 'PEMBERIAN IMUNISASI');
                $sheet->setCellValue('AB9', 'ORALIT');
                $sheet->setCellValue('AB10', 'bl');
                $sheet->setCellValue('AC9', 'HB0');
                $sheet->setCellValue('AC10', 'bl');
                $sheet->setCellValue('AD9', 'BCG');
                $sheet->setCellValue('AD10', 'bl');
                $sheet->mergeCells('AE9:AH9');
                $sheet->setCellValue('AE9', 'POLIO');
                $sheet->setCellValue('AE10', 'bl');
                $sheet->setCellValue('AF10', 'bl');
                $sheet->setCellValue('AG10', 'bl');
                $sheet->setCellValue('AH10', 'bl');
                $sheet->mergeCells('AI9:AK9');
                $sheet->setCellValue('AI9', 'DPT/HB');
                $sheet->setCellValue('AI10', 'bl');
                $sheet->setCellValue('AJ10', 'bl');
                $sheet->setCellValue('AK10', 'bl');
                $sheet->mergeCells('AL9:AN9');
                $sheet->setCellValue('AL9', 'PCV');
                $sheet->setCellValue('AL10', 'bl');
                $sheet->setCellValue('AM10', 'bl');
                $sheet->setCellValue('AN10', 'bl');
                $sheet->setCellValue('AO9', 'IPV');
                $sheet->setCellValue('AO10', 'bl');
                $sheet->setCellValue('AP9', 'CAMPAK');
                $sheet->setCellValue('AP10', 'bl');

                $sheet->mergeCells('AQ8:AQ10');
                $sheet->setCellValue('AQ8', 'Tanggal Balita Menimbang');

                $sheet->mergeCells('AR8:AR10');
                $sheet->setCellValue('AR8', 'CATATAN');

                // Style Heading


                $sheet->getStyle('A8:AR10')->getFont()->setBold(true);
                $sheet->getStyle('A8:AR10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A8:AR10')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $row = 11;
                foreach ($posyandu->bayis as $index => $bayi) {

                    $sheet->setCellValue("A{$row}", $index + 1); // NO
                    $sheet->setCellValue("B{$row}", $bayi->namabalita ?? '-'); // Nama Balita/Bayi
                    $sheet->setCellValue("C{$row}", $bayi->tanggal_lahir ?? '-'); // Tanggal Lahir
                    $sheet->setCellValue("D{$row}", $bayi->beratbadan_lahir ?? '-'); // Berat Badan Lahir
                    $sheet->setCellValue("E{$row}", $bayi->pus->nama_suami ?? '-'); // Nama Ayah
                    $sheet->setCellValue("F{$row}", $bayi->pus->wus->nama ?? '-'); // Nama Ibu
                    $sheet->setCellValue("G{$row}", $bayi->pus->kelompok_dasawisma ?? '-'); // Kelompok Dasawisma

                    // Hasil Penimbangan KG (Jan - Des)
                    $penimbangan = $bayi->periksabalita ?? collect();

                    for ($month = 1; $month <= 12; $month++) {
                        $monthString = str_pad($month, 2, '0', STR_PAD_LEFT);
                        $column = chr(72 + $month - 1); // Mulai dari kolom 'H'

                        $latestCheck = $penimbangan
                            ->filter(fn($p) => date('m', strtotime($p->Kegiatanposyandu->tgl_kegiatan ?? '')) === $monthString)
                            ->sortByDesc(fn($p) => strtotime($p->Kegiatanposyandu->tgl_kegiatan ?? ''))
                            ->first();

                        if ($latestCheck) {
                            $sheet->setCellValue("{$column}{$row}", "{$latestCheck->berat_badan}/{$latestCheck->panjang_badan}");
                        } else {
                            $sheet->setCellValue("{$column}{$row}", '-');
                        }
                    }

                    // Pemberian ASI E1 - E6
                    $sheet->setCellValue("T{$row}", $bayi->periksabalita->firstWhere('pemberian_asi_kuartal', '1') ? 'Ya' : '-');
                    $sheet->setCellValue("U{$row}", $bayi->periksabalita->firstWhere('pemberian_asi_kuartal', '2') ? 'Ya' : '-');
                    $sheet->setCellValue("V{$row}", $bayi->periksabalita->firstWhere('pemberian_asi_kuartal', '3') ? 'Ya' : '-');
                    $sheet->setCellValue("W{$row}", $bayi->periksabalita->firstWhere('pemberian_asi_kuartal', '4') ? 'Ya' : '-');
                    $sheet->setCellValue("X{$row}", $bayi->periksabalita->firstWhere('pemberian_asi_kuartal', '5') ? 'Ya' : '-');
                    $sheet->setCellValue("Y{$row}", $bayi->periksabalita->firstWhere('pemberian_asi_kuartal', '6') ? 'Ya' : '-');

                    // Pelayanan Vitamin A -
                    $sheet->setCellValue("Z{$row}", $this->getTanggalVitamin($bayi->periksabalita, '1'));
                    $sheet->setCellValue("AA{$row}",  $this->getTanggalVitamin($bayi->periksabalita, '2'));



                    // Gunakan metode getTanggalImunisasi dalam collection()
                    $sheet->setCellValue("AB{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'ORALIT'));
                    $sheet->setCellValue("AC{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'HB0'));
                    $sheet->setCellValue("AD{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'BCG'));
                    $sheet->setCellValue("AE{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'Polio Tetes 1'));
                    $sheet->setCellValue("AF{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'Polio Tetes 2'));
                    $sheet->setCellValue("AG{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'Polio Tetes 3'));
                    $sheet->setCellValue("AH{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'Polio Tetes 4'));
                    $sheet->setCellValue("AI{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'DPT/HB 1'));
                    $sheet->setCellValue("AJ{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'DPT/HB 2'));
                    $sheet->setCellValue("AK{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'DPT/HB 3'));
                    $sheet->setCellValue("AL{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'PCV 1'));
                    $sheet->setCellValue("AM{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'PCV 2'));
                    $sheet->setCellValue("AN{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'PCV 3'));
                    $sheet->setCellValue("AO{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'IPV'));
                    $sheet->setCellValue("AP{$row}", $this->getTanggalImunisasi($bayi->periksabalita, 'CAMPAK'));


                    // Kosongkan AQ dan AR
                    $sheet->setCellValue("AQ{$row}", '');
                    $sheet->setCellValue("AR{$row}", '');

                    $row++;
                }

                // dd($bayi);




                // Border untuk data
                $sheet->getStyle("A10:AR" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
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
        return 'Form 2'; // Nama worksheet untuk Sheet 1
    }
}
