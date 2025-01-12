<?php

namespace App\Exports;

use DateTime;
use App\Models\Bayi;
use App\Models\Posyandu;
use App\Models\PeriksaBalita;
use Illuminate\Support\Carbon;
use App\Models\KegiatanPosyandu;
use App\Models\periksawus;
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

class HasilKegiatan implements WithHeadings, WithMapping, WithEvents, WithTitle
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


    private function BGM($umurBulan, $beratbadan, $jeniskelamin)
    {
        if ($jeniskelamin == 'Laki-laki') {
            $median = [
                3.3,
                4.5,
                5.6,
                6.4,
                7,
                7.5,
                7.9,
                8.3,
                8.6,
                8.9,
                9.2,
                9.4,
                9.6,
                9.9,
                10.1,
                10.3,
                10.5,
                10.7,
                10.9,
                11.1,
                11.3,
                11.5,
                11.8,
                12,
                12.2,
                12.4,
                12.5,
                12.7,
                12.9,
                13.1,
                13.3,
                13.5,
                13.7,
                13.8,
                14,
                14.2,
                14.3,
                14.5,
                14.7,
                14.8,
                15,
                15.2,
                15.3,
                15.5,
                15.7,
                15.8,
                16,
                16.2,
                16.3,
                16.5,
                16.7,
                16.8,
                17,
                17.2,
                17.3,
                17.5,
                17.7,
                17.8,
                18,
                18.2,
                18.3
            ];
            $plus1sd = [
                3.9,
                5.1,
                6.3,
                7.2,
                7.8,
                8.4,
                8.8,
                9.2,
                9.6,
                9.9,
                10.2,
                10.5,
                10.8,
                11,
                11.3,
                11.5,
                11.7,
                12,
                12.2,
                12.5,
                12.7,
                12.9,
                13.2,
                13.4,
                13.6,
                13.9,
                14.1,
                14.3,
                14.5,
                14.8,
                15,
                15.2,
                15.4,
                15.6,
                15.8,
                16,
                16.2,
                16.4,
                16.6,
                16.8,
                17,
                17.2,
                17.4,
                17.6,
                17.8,
                18,
                18.2,
                18.4,
                18.6,
                18.8,
                19,
                19.2,
                19.4,
                19.6,
                19.8,
                20,
                20.2,
                20.4,
                20.6,
                20.8,
                21
            ];
            $minus1sd = [
                2.9,
                3.9,
                4.9,
                5.7,
                6.2,
                6.7,
                7.1,
                7.4,
                7.7,
                8,
                8.2,
                8.4,
                8.6,
                8.8,
                9,
                9.2,
                9.4,
                9.6,
                9.8,
                10,
                10.1,
                10.3,
                10.5,
                10.7,
                10.8,
                11,
                11.2,
                11.3,
                11.5,
                11.7,
                11.8,
                12,
                12.1,
                12.3,
                12.4,
                12.6,
                12.7,
                12.9,
                13,
                13.1,
                13.3,
                13.4,
                13.6,
                13.7,
                13.8,
                14,
                14.1,
                14.3,
                14.4,
                14.5,
                14.7,
                14.8,
                15,
                15.1,
                15.2,
                15.4,
                15.5,
                15.6,
                15.8,
                15.9,
                16
            ];
            $bbu = 0;
            if ($umurBulan <= 60) {
                if ($beratbadan > $median[$umurBulan]) {
                    $hitungatas = $beratbadan - $median[$umurBulan];
                    $hitungbawah = $plus1sd[$umurBulan] - $median[$umurBulan];
                    $bbu = $hitungatas / $hitungbawah;
                } else {
                    $hitungatas = $beratbadan - $median[$umurBulan];
                    $hitungbawah = $median[$umurBulan] - $minus1sd[$umurBulan];
                    $bbu = $hitungatas / $hitungbawah;
                }
                if ($bbu < -2) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } elseif ($jeniskelamin == 'Perempuan') {
            $minus1sd = [
                2.8,
                3.6,
                4.5,
                5.2,
                5.7,
                6.1,
                6.5,
                6.8,
                7,
                7.3,
                7.5,
                7.7,
                7.9,
                8.1,
                8.3,
                8.5,
                8.7,
                8.9,
                9.1,
                9.2,
                9.4,
                9.6,
                9.8,
                10,
                10.2,
                10.3,
                10.5,
                10.7,
                10.9,
                11.1,
                11.2,
                11.4,
                11.6,
                11.7,
                11.9,
                12,
                12.2,
                12.4,
                12.5,
                12.7,
                12.8,
                13,
                13.1,
                13.3,
                13.4,
                13.6,
                13.7,
                13.9,
                14,
                14.2,
                14.3,
                14.5,
                14.6,
                14.8,
                14.9,
                15.1,
                15.2,
                15.3,
                15.5,
                15.6,
                15.8
            ];
            $median = [
                3.2,
                4.2,
                5.1,
                5.8,
                6.4,
                6.9,
                7.3,
                7.6,
                7.9,
                8.2,
                8.5,
                8.7,
                8.9,
                9.2,
                9.4,
                9.6,
                9.8,
                10,
                10.2,
                10.4,
                10.6,
                10.9,
                11.1,
                11.3,
                11.5,
                11.7,
                11.9,
                12.1,
                12.3,
                12.5,
                12.7,
                12.9,
                13.1,
                13.3,
                13.5,
                13.7,
                13.9,
                14,
                14.2,
                14.4,
                14.6,
                14.8,
                15,
                15.2,
                15.3,
                15.5,
                15.7,
                15.9,
                16.1,
                16.3,
                16.4,
                16.6,
                16.8,
                17,
                17.2,
                17.3,
                17.5,
                17.7,
                17.9,
                18,
                18.2
            ];
            $plus1sd = [
                3.7,
                4.8,
                5.8,
                6.6,
                7.3,
                7.8,
                8.2,
                8.6,
                9,
                9.3,
                9.6,
                9.9,
                10.1,
                10.4,
                10.6,
                10.9,
                11.1,
                11.4,
                11.6,
                11.8,
                12.1,
                12.3,
                12.5,
                12.8,
                13,
                13.3,
                13.5,
                13.7,
                14,
                14.2,
                14.4,
                14.7,
                14.9,
                15.1,
                15.4,
                15.6,
                15.8,
                16,
                16.3,
                16.5,
                16.7,
                16.9,
                17.2,
                17.4,
                17.6,
                17.8,
                18.1,
                18.3,
                18.5,
                18.8,
                19,
                19.2,
                19.4,
                19.7,
                19.9,
                20.1,
                20.3,
                20.6,
                20.8,
                21,
                21.2
            ];
            $bbu = 0;
            if ($umurBulan <= 60) {
                if ($beratbadan > $median[$umurBulan]) {
                    $hitungatas = $beratbadan - $median[$umurBulan];
                    $hitungbawah = $plus1sd[$umurBulan] - $median[$umurBulan];
                    $bbu = $hitungatas / $hitungbawah;
                } else {
                    $hitungatas = $beratbadan - $median[$umurBulan];
                    $hitungbawah = $median[$umurBulan] - $minus1sd[$umurBulan];
                    $bbu = $hitungatas / $hitungbawah;
                }
                if ($bbu < -2) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    private function getjumlahImunisasi($periksabalita, $namaImunisasi)
    {
        foreach ($periksabalita as $periksa) {
            if ($periksa->imunisasi->contains('nama', $namaImunisasi)) {
                return $periksa->Kegiatanposyandu->tgl_kegiatan ?? '-';
            }
        }
        return '-';
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
                    'wus.pus.bayi.PeriksaBalita.imunisasi' => function ($query) {
                        $query->whereYear('imunisasi_balitas.created_at', $this->tahun);
                    },
                    'wus.periksawus.kegiatanposyandu' => function ($query) {
                        $query->whereYear(DB::raw("STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, '%d-%m-%Y')"), $this->tahun);
                    },
                    'wus.pus.alatKontrasepsis' => function ($query) {
                        $query->withPivot(['tanggal_pertama_pakai'])
                            ->whereYear(DB::raw("STR_TO_DATE(pivot_kontrasepsi_puses.tanggal_pertama_pakai, '%d-%m-%Y')"), $this->tahun);
                    }
                ])->find($this->idPosyandu);


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

                $sheet->mergeCells('B8:E8');
                $sheet->setCellValue('B8', 'IBU HAMIL');
                $sheet->mergeCells('B9:B10');
                $sheet->setCellValue('B9', 'BULAN');
                $sheet->mergeCells('C9:C10');
                $sheet->setCellValue('C9', 'JUMLAH');
                $sheet->mergeCells('D9:D10');
                $sheet->setCellValue('D9', 'JUMLAH YANG MEMERIKSAKAN DIRI');
                $sheet->mergeCells('E9:E10');
                $sheet->setCellValue('E9', 'JUMLAH YANG MENDAPAT FE');

                $sheet->mergeCells('F8:F10');
                $sheet->setCellValue('F8', 'JUMLAH YANG MENYUSUI');
                $sheet->mergeCells('G8:G10');
                $sheet->setCellValue('G8', 'JUMLAH IBU NIFAS YANG MENDAPAT KAPSUL VITAMIN A');

                $sheet->mergeCells('H8:J8');
                $sheet->setCellValue('H8', 'JUMLAH PESERTA KB YANG MENDAPAT PELAYANAN ULANG');
                $sheet->mergeCells('H9:H10');
                $sheet->setCellValue('H9', 'KONDOM');
                $sheet->mergeCells('I9:I10');
                $sheet->setCellValue('I9', 'PIL');
                $sheet->mergeCells('J9:J10');
                $sheet->setCellValue('J9', 'SUNTIK');

                $sheet->mergeCells('K8:O8');
                $sheet->setCellValue('K8', 'PENIMBANGAN BAYI DAN BALITA (JUMLAH)');
                $sheet->mergeCells('K9:K10');
                $sheet->setCellValue('K9', 'JUMLAH BALITA SASARAN POSYANDU (S)');
                $sheet->mergeCells('L9:L10');
                $sheet->setCellValue('L9', 'YANG MEMILIKI KMS / BUKU KIA');
                $sheet->mergeCells('M9:M10');
                $sheet->setCellValue('M9', 'YANG DITIMBANG (D)');
                $sheet->mergeCells('N9:N10');
                $sheet->setCellValue('N9', 'YANG NAIK (N)');
                $sheet->mergeCells('O9:O10');
                $sheet->setCellValue('O9', 'BGM');

                $sheet->mergeCells('P8:Q8');
                $sheet->setCellValue('P8', 'JUMLAH BAYI DAN BALITA');
                $sheet->mergeCells('P9:P10');
                $sheet->setCellValue('P9', 'YANG MENDAPAT KAPSUL VIT A');
                $sheet->mergeCells('Q9:Q10');
                $sheet->setCellValue('Q9', 'YANG MENDAPAT PMT PENYULUHAN');

                $sheet->mergeCells('R8:AE8');
                $sheet->setCellValue('R8', 'JUMLAH BAYI DAN BALITA');
                $sheet->mergeCells('R9:R10');
                $sheet->setCellValue('R9', 'HB0');
                $sheet->mergeCells('S9:S10');
                $sheet->setCellValue('S9', 'BCG');
                $sheet->mergeCells('T9:W9');
                $sheet->setCellValue('T9', 'POLIO');
                $sheet->setCellValue('T10', 'I');
                $sheet->setCellValue('U10', 'II');
                $sheet->setCellValue('V10', 'III');
                $sheet->setCellValue('W10', 'IV');
                $sheet->mergeCells('X9:Z9');
                $sheet->setCellValue('X9', 'DPT / HB');
                $sheet->setCellValue('X10', 'I');
                $sheet->setCellValue('Y10', 'II');
                $sheet->setCellValue('Z10', 'III');
                $sheet->mergeCells('AA9:AC9');
                $sheet->setCellValue('AA9', 'PCV');
                $sheet->setCellValue('AA10', 'I');
                $sheet->setCellValue('AB10', 'II');
                $sheet->setCellValue('AC10', 'III');
                $sheet->mergeCells('AD9:AD10');
                $sheet->setCellValue('AD9', 'IPV');
                $sheet->mergeCells('AE9:AE10');
                $sheet->setCellValue('AE9', 'CAMPAK');

                $sheet->mergeCells('AF8:AJ8');
                $sheet->setCellValue('AF8', 'JUMLAH WUS DAN BUMIL YANG DAPAT IMUNISASI TT');
                $sheet->mergeCells('AF9:AF10');
                $sheet->setCellValue('AF9', 'I');
                $sheet->mergeCells('AG9:AG10');
                $sheet->setCellValue('AG9', 'II');
                $sheet->mergeCells('AH9:AH10');
                $sheet->setCellValue('AH9', 'III');
                $sheet->mergeCells('AI9:AI10');
                $sheet->setCellValue('AI9', 'IV');
                $sheet->mergeCells('AJ9:AJ10');
                $sheet->setCellValue('AJ9', 'V');

                $sheet->mergeCells('AK8:AL8');
                $sheet->setCellValue('AK8', 'BALITA YANG MENDERITA DIARE');
                $sheet->mergeCells('AK9:AK10');
                $sheet->setCellValue('AK9', 'JUMLAH BALITA');
                $sheet->mergeCells('AL9:AL10');
                $sheet->setCellValue('AL9', 'JUMLAH YANG MENDAPAT ORALIT');

                $sheet->mergeCells('AM8:AM10');
                $sheet->setCellValue('AM8', 'KETERANGAN');


                $sheet->getStyle('A8:AM10')->getFont()->setBold(true);
                $sheet->getStyle('A8:AM10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A8:AM10')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $bulanSekarang = date('n');

                $row = 11;

                for ($month = 1; $month <= $bulanSekarang; $month++) {
                    $bulan = DateTime::createFromFormat('!m', $month)->format('F');
                    $tahun = $this->tahun;
                    $jumlahBGM = 0;

                    $jumlahHamil = $posyandu->wus->where('statusperiksa', 'Hamil')->count();

                    $jumlahMemeriksakanDiri = $posyandu->wus->flatMap(function ($wus) use ($month, $tahun) {
                        return $wus->periksawus->filter(function ($periksa) use ($month, $tahun) {
                            $tanggal = DateTime::createFromFormat('d-m-Y', $periksa->kegiatanposyandu->tgl_kegiatan ?? '');
                            return $tanggal && $tanggal->format('Y') == $tahun && $tanggal->format('n') == $month;
                        });
                    })->count();

                    $jumlahMendapatFE = $posyandu->wus->filter(function ($wus) {
                        return $wus->periksawus->contains(function ($periksa) {
                            return !is_null($periksa->tablettambah_darahs_kuartal);
                        });
                    })->count();

                    $sheet->setCellValue("B$row", $bulan);
                    $sheet->setCellValue("C$row", $jumlahHamil);
                    $sheet->setCellValue("D$row", $jumlahMemeriksakanDiri);
                    $sheet->setCellValue("E$row", $jumlahMendapatFE);

                    $jumlahMenyusui = $posyandu->wus->where('statusperiksa', 'Tidak hamil & Menyusui')->count();

                    $jumlahVitaminA = $posyandu->wus->filter(function ($wus) {
                        return $wus->periksawus->contains(function ($periksa) {
                            return !is_null($periksa->vitamin_kuartal);
                        });
                    })->count();

                    $sheet->setCellValue("F$row", $jumlahMenyusui);
                    $sheet->setCellValue("G$row", $jumlahVitaminA);

                    foreach (['Kondom', 'Pil', 'Suntik'] as $metode) {
                        ${"jumlah$metode"} = $posyandu->wus->flatMap(function ($wus) use ($month, $tahun, $metode) {
                            $alatKontrasepsis = optional($wus->pus)->alatKontrasepsis;
                            if ($alatKontrasepsis) {
                                return $alatKontrasepsis->where('nama', $metode)->filter(function ($alat) use ($month, $tahun) {
                                    $tanggal = DateTime::createFromFormat('d-m-Y', optional($alat->pivot)->tanggal_pertama_pakai);
                                    return $tanggal && $tanggal->format('Y') == $tahun && $tanggal->format('n') == $month;
                                });
                            }
                            return collect();
                        })->count() ?: 0;
                    }

                    $sheet->setCellValue("H$row", $jumlahKondom);
                    $sheet->setCellValue("I$row", $jumlahPil);
                    $sheet->setCellValue("J$row", $jumlahSuntik);

                    $tanggalAkhirBulan = \Carbon\Carbon::create($tahun, $month)->endOfMonth()->format('Y-m-d');
                    $periksaTerbaru = PeriksaBalita::whereHas('Kegiatanposyandu', function ($query) use ($month, $tahun) {
                        $query->whereMonth(DB::raw("STR_TO_DATE(tgl_kegiatan, '%d-%m-%Y')"), $month)
                            ->whereYear(DB::raw("STR_TO_DATE(tgl_kegiatan, '%d-%m-%Y')"), $tahun);
                    })->with('Kegiatanposyandu')
                        ->get()
                        ->sortByDesc(function ($periksa) {
                            return DateTime::createFromFormat('d-m-Y', $periksa->kegiatanposyandu->tgl_kegiatan ?? '01-01-1900')->getTimestamp();
                        })->first();


                    if ($periksaTerbaru) {
                        $beratBadan = $periksaTerbaru->berat_badan;
                        $tanggalLahir = $periksaTerbaru->bayi->tanggal_lahir;

                        if ($tanggalLahir) {
                            $tanggalLahir = \Carbon\Carbon::createFromFormat('d-m-Y', $tanggalLahir);

                            $tanggalSekarang = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalAkhirBulan);
                            $umurDalamBulan = $tanggalSekarang->diffInMonths($tanggalLahir);

                            if ($this->BGM($umurDalamBulan, $beratBadan, $periksaTerbaru->bayi->jenis_kelamin)) {
                                $jumlahBGM++;
                            }
                        }
                    }

                    $sheet->setCellValue("O$row", $jumlahBGM);

                    $kegiatanbalitaselesai = KegiatanPosyandu::where('posyandu_id', $this->idPosyandu)
                        ->where('status_kegiatan', 'selesai')
                        ->where('tipe_kegiatan', 'Balita')
                        ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                        ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun])
                        ->count();

                    $afiliasbalita = Bayi::where('posyandus_id', $this->idPosyandu)->count();
                    $targetpesertabalita = $kegiatanbalitaselesai * $afiliasbalita;

                    $jumlahKMSBalita = Bayi::where('posyandus_id', $this->idPosyandu)->count();
                    $id = $this->idPosyandu;
                    $jumlahPesertaBalitaTotal = PeriksaBalita::whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();

                    $jumlahNaikBeratBadan = 0;

                    $pemeriksaanBulanIni = PeriksaBalita::whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->get();

                    foreach ($pemeriksaanBulanIni as $periksa) {
                        $pemeriksaanBulanLalu = PeriksaBalita::where('bayis_id', $periksa->bayis_id)
                            ->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                                $query->where('posyandu_id', $id)
                                    ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month - 1])
                                    ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                            })->latest('id')->first();

                        if ($pemeriksaanBulanLalu && $periksa->berat_badan > $pemeriksaanBulanLalu->berat_badan) {
                            $jumlahNaikBeratBadan++;
                        }
                    }

                    $sheet->setCellValue("K$row", $targetpesertabalita);
                    $sheet->setCellValue("L$row", $jumlahKMSBalita);
                    $sheet->setCellValue("M$row", $jumlahPesertaBalitaTotal);
                    $sheet->setCellValue("N$row", $jumlahNaikBeratBadan);

                    $jumlahVitaminA = PeriksaBalita::whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->whereNotNull('vitamin_kuartal')->count();

                    $jumlahPMT = "";

                    $sheet->setCellValue("P$row", $jumlahVitaminA);
                    $sheet->setCellValue("Q$row", $jumlahPMT);

                    $jumlahHB0 = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'HB0');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();

                    $jumlahBCG = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'BCG');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();

                    $jumlahPolioTetes1 = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'Polio Tetes 1');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();

                    $sheet->setCellValue("R$row", $jumlahHB0);
                    $sheet->setCellValue("S$row", $jumlahBCG);
                    $sheet->setCellValue("T$row", $jumlahPolioTetes1);


                    $jumlahPolioTetes2 = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'Polio Tetes 2');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();
                    $jumlahPolioTetes3 = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'Polio Tetes 3');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();
                    $jumlahPolioTetes4 = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'Polio Tetes 4');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();

                    $sheet->setCellValue("U$row", $jumlahPolioTetes2);
                    $sheet->setCellValue("v$row", $jumlahPolioTetes3);
                    $sheet->setCellValue("W$row", $jumlahPolioTetes4);

                    $DPT1 = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'DPT 1');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();
                    $DPT2 = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'DPT 2');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();
                    $DPT3 = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'DPT3');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();
                    $sheet->setCellValue("X$row", $DPT1);
                    $sheet->setCellValue("Y$row", $DPT2);
                    $sheet->setCellValue("Z$row", $DPT3);

                    $PCV1 = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'PCV1');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();
                    $PCV2 = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'PCV2');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();
                    $PCV3 = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'PCV3');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();
                    $sheet->setCellValue("AA$row", $PCV1);
                    $sheet->setCellValue("AB$row", $PCV2);
                    $sheet->setCellValue("AC$row", $PCV3);

                    $IPV = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'IPV');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();
                    $CAMPAK = PeriksaBalita::whereHas('imunisasi', function ($query) {
                        $query->where('nama', 'Campak');
                    })->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                        $query->where('posyandu_id', $id)
                            ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                            ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                    })->count();
                    $sheet->setCellValue("AD$row", $IPV);
                    $sheet->setCellValue("AE$row", $CAMPAK);

                    $imunisasitt1 = periksawus::where('imunisasi_kehamilans_kuartal', '1')
                        ->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                            $query->where('posyandu_id', $id)
                                ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                                ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                        })->count();

                    $imunisasitt2 = periksawus::where('imunisasi_kehamilans_kuartal', '2')
                        ->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                            $query->where('posyandu_id', $id)
                                ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                                ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                        })->count();
                    $imunisasitt3 = periksawus::where('imunisasi_kehamilans_kuartal', '3')
                        ->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                            $query->where('posyandu_id', $id)
                                ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                                ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                        })->count();
                    $imunisasitt4 = periksawus::where('imunisasi_kehamilans_kuartal', '4')
                        ->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                            $query->where('posyandu_id', $id)
                                ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                                ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                        })->count();
                    $imunisasitt5 = periksawus::where('imunisasi_kehamilans_kuartal', '5')
                        ->whereHas('kegiatanposyandu', function ($query) use ($month, $tahun, $id) {
                            $query->where('posyandu_id', $id)
                                ->whereRaw('MONTH(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$month])
                                ->whereRaw('YEAR(STR_TO_DATE(tgl_kegiatan, "%d-%m-%Y")) = ?', [$tahun]);
                        })->count();

                    $sheet->setCellValue("AF$row", $imunisasitt1);
                    $sheet->setCellValue("AG$row", $imunisasitt2);
                    $sheet->setCellValue("AH$row", $imunisasitt3);
                    $sheet->setCellValue("AI$row", $imunisasitt4);
                    $sheet->setCellValue("AJ$row", $imunisasitt5);

                    $row++;
                }

                $sheet->getStyle("A10:AM" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
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
        return 'Form 6';
    }
}
