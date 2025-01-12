<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bayi;
use App\Models\PeriksaBalita;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BayiController extends Controller
{
    public function getUmurDalamBulan($tanggalLahir, $waktuperiksa)
    {
        $tanggalLahir = Carbon::createFromFormat('d-m-Y', $tanggalLahir);
        $waktuperiksa = Carbon::createFromFormat('d-m-Y', $waktuperiksa);

        $umurDalamBulan = $tanggalLahir->diffInMonths($waktuperiksa);

        return $umurDalamBulan;
    }
    public function getpesertabalita()
    {
        return response()->json(Bayi::get());
    }

    public function getJumlahBalita()
    {
        $hasil = Bayi::count();
        return response()->json($hasil);
    }
    public function storeBayi(Request $request)
    {
        Bayi::create([
            'pus_id' => $request->pus_id,
            'namabalita' => $request->namabalita,
            'tanggal_lahir' => $request->tanggal_lahir,
            'beratbadan_lahir' => $request->beratbadan_lahir,
            'panjangbadan_lahir' => $request->panjangbadan_lahir,
            'likep_lahir' => $request->likep_lahir,
            'posyandus_id' => $request->posyandus_id,
            'jenis_kelamin' => $request->jenis_kelamin
        ]);
        return response()->json('berhasil');
    }

    public function updateBayis(Request $request)
    {
        $updated = Bayi::where('id', $request->id)->update([
            'tanggal_lahir' => $request->tanggal_lahir,
            'beratbadan_lahir' => $request->beratbadan_lahir,
            'panjangbadan_lahir' => $request->panjangbadan_lahir,
            'likep_lahir' => $request->likep_lahir,
            'posyandus_id' => $request->posyandus_id
        ]);

        return response()->json($updated ? 'Update successful' : 'Update failed');
    }

    public function getBayi()
    {
        $bayis = Bayi::with(['posyandu'])->get();

        if ($bayis->isEmpty()) {
            return response()->json(['message' => 'Data bayi tidak ditemukan'], 404);
        }

        $return = $bayis->map(function ($bayi) {
            return [
                'beratbadan_lahir' => $bayi->beratbadan_lahir,
                'panjangbadan_lahir' => $bayi->panjangbadan_lahir,
                'likep_lahir' => $bayi->likep_lahir,
                'jenis_kelamin' => $bayi->jenis_kelamin,
                'balitas_id' => $bayi->id,
                'nama' => $bayi->namabalita,
                'tanggal_lahir' => $bayi->tanggal_lahir,
                'posyandu_nama' => optional($bayi->posyandu)->nama_posyandu,
                'posyandus_id' => optional($bayi->posyandu)->id
            ];
        });

        return response()->json($return);
    }
    public function getBayiPosyandu($id)
    {
        $bayis = Bayi::with(['posyandu'])->where('posyandus_id', $id)->get();

        if ($bayis->isEmpty()) {
            return response()->json(['message' => 'Data bayi tidak ditemukan'], 404);
        }

        $return = $bayis->map(function ($bayi) {
            return [
                'beratbadan_lahir' => $bayi->beratbadan_lahir,
                'panjangbadan_lahir' => $bayi->panjangbadan_lahir,
                'likep_lahir' => $bayi->likep_lahir,
                'jenis_kelamin' => $bayi->jenis_kelamin,
                'balitas_id' => $bayi->id,
                'nama' => $bayi->namabalita,
                'tanggal_lahir' => $bayi->tanggal_lahir,
                'posyandu_nama' => optional($bayi->posyandu)->nama_posyandu,
                'posyandus_id' => optional($bayi->posyandu)->id
            ];
        });

        return response()->json($return);
    }
    public function getAnak($id)
    {
        return response()->json(Bayi::where('pus_id', $id)->get());
    }

    public function deleteBayi($id)
    {
        Bayi::where('id', $id)->delete();
        return response()->json('Dihapus');
    }

    public function getJumlahBalitaPosyandu($id)
    {
        return response()->json(Bayi::where('posyandus_id', $id)->count());
    }


    public function getberatbadanumur()
    {
        $bayis = Bayi::with(['PeriksaBalita' => function ($query) {
            $query->join('kegiatan_posyandus', 'periksa_balitas.kegiatanposyandu_balita_id', '=', 'kegiatan_posyandus.id')
                ->whereHas('Kegiatanposyandu', function ($subQuery) {
                    $subQuery->where('status_kegiatan', 'selesai');
                })
                ->orderByRaw('STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y") DESC');
        }])->get();
        $result = $bayis->map(function ($bayi) {
            $latestPeriksa = $bayi->PeriksaBalita->first();
            $tglKegiatan = $latestPeriksa ? $latestPeriksa->tgl_kegiatan : null;
            $umurDalamBulan = $tglKegiatan ? $this->getUmurDalamBulan($bayi->tanggal_lahir, $tglKegiatan) : null;
            return [
                'id' => $bayi->id,
                'nama' => $bayi->namabalita,
                'umur_dalam_bulan' => $umurDalamBulan,
                'jenis_kelamin' => $bayi->jenis_kelamin,
                'berat_badan' => $latestPeriksa ? $latestPeriksa->berat_badan : null,
            ];
        });
        $bayiByGender = $result->groupBy('jenis_kelamin');

        $bayiLakiLaki = $bayiByGender->get('Laki-laki', collect());
        $bayiPerempuan = $bayiByGender->get('Perempuan', collect());

        $hasilLaki = $bayiLakiLaki->map(function ($bayi) {
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
            $jenis = '';

            if ($bayi['umur_dalam_bulan'] >= 0 && $bayi['umur_dalam_bulan'] <= 60) {
                $umurBulan = $bayi['umur_dalam_bulan'];

                if (!is_null($umurBulan) && isset($median[$umurBulan])) {
                    if ($bayi['berat_badan'] > $median[$umurBulan]) {
                        $hitungatas = $bayi['berat_badan'] - $median[$umurBulan];
                        $hitungbawah = $plus1sd[$umurBulan] - $median[$umurBulan];
                        $bbu = $hitungatas / $hitungbawah;
                    } else {
                        $hitungatas = $bayi['berat_badan'] - $median[$umurBulan];
                        $hitungbawah = $median[$umurBulan] - $minus1sd[$umurBulan];
                        $bbu = $hitungatas / $hitungbawah;
                    }
                    if ($bbu < -3) {
                        $jenis = 'Berat Badan Sangat Kurang';
                    } elseif ($bbu >= -3 && $bbu < -2) {
                        $jenis = 'Berat Badan Kurang';
                    } elseif ($bbu >= -2 && $bbu <= 1) {
                        $jenis = 'Berat Badan Normal';
                    } elseif ($bbu > 1) {
                        $jenis = 'Resiko Berat Badan Lebih';
                    }
                }
            }

            return [
                'jenis_kelamin' => $bayi['jenis_kelamin'],
                'hitungatas' => $hitungatas,
                'hitungbawah' => $hitungbawah,
                'id' => $bayi['id'],
                'nama' => $bayi['nama'],
                'umur_dalam_bulan' => $bayi['umur_dalam_bulan'],
                'berat_badan' => $bayi['berat_badan'],
                'bbu' => $bbu,
                'jenis' => $jenis,
            ];
        });


        $hasilPerempuan = $bayiPerempuan->map(function ($bayi) {
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
            $jenis = '';

            if ($bayi['umur_dalam_bulan'] >= 0 && $bayi['umur_dalam_bulan'] <= 60) {
                $umurBulan = $bayi['umur_dalam_bulan'];

                if (!is_null($umurBulan) && isset($median[$umurBulan])) {
                    if ($bayi['berat_badan'] > $median[$umurBulan]) {
                        $hitungatas = $bayi['berat_badan'] - $median[$umurBulan];
                        $hitungbawah = $plus1sd[$umurBulan] - $median[$umurBulan];
                        $bbu = $hitungatas / $hitungbawah;
                    } else {
                        $hitungatas = $bayi['berat_badan'] - $median[$umurBulan];
                        $hitungbawah = $median[$umurBulan] - $minus1sd[$umurBulan];
                        $bbu = $hitungatas / $hitungbawah;
                    }
                    if ($bbu < -3) {
                        $jenis = 'Berat Badan Sangat Kurang';
                    } elseif ($bbu >= -3 && $bbu < -2) {
                        $jenis = 'Berat Badan Kurang';
                    } elseif ($bbu >= -2 && $bbu <= 1) {
                        $jenis = 'Berat Badan Normal';
                    } elseif ($bbu > 1) {
                        $jenis = 'Resiko Berat Badan Lebih';
                    }
                }
            }

            return [
                'id' => $bayi['id'],
                'nama' => $bayi['nama'],
                'umur_dalam_bulan' => $bayi['umur_dalam_bulan'],
                'berat_badan' => $bayi['berat_badan'],
                'bbu' => $bbu,
                'jenis' => $jenis,
                'jenis_kelamin' => $bayi['jenis_kelamin']
            ];
        });

        return response()->json(array_merge($hasilLaki->toArray(), $hasilPerempuan->toArray()));
    }

    public function getberatbadanumurpos($id)
    {
        $bayis = Bayi::with(['PeriksaBalita' => function ($query) {
            $query->join('kegiatan_posyandus', 'periksa_balitas.kegiatanposyandu_balita_id', '=', 'kegiatan_posyandus.id')
                ->whereHas('Kegiatanposyandu', function ($subQuery) {
                    $subQuery->where('status_kegiatan', 'selesai');
                })
                ->orderByRaw('STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y") DESC');
        }])->where('posyandus_id', $id)->get();

        $result = $bayis->map(function ($bayi) {
            $latestPeriksa = $bayi->PeriksaBalita->first();
            $tglKegiatan = $latestPeriksa ? $latestPeriksa->tgl_kegiatan : null;
            $umurDalamBulan = $tglKegiatan ? $this->getUmurDalamBulan($bayi->tanggal_lahir, $tglKegiatan) : null;


            return [
                'id' => $bayi->id,
                'nama' => $bayi->namabalita,
                'umur_dalam_bulan' => $umurDalamBulan,
                'jenis_kelamin' => $bayi->jenis_kelamin,
                'berat_badan' => $latestPeriksa ? $latestPeriksa->berat_badan : null,
            ];
        });

        $bayiByGender = $result->groupBy('jenis_kelamin');

        $bayiLakiLaki = $bayiByGender->get('Laki-laki', collect());
        $bayiPerempuan = $bayiByGender->get('Perempuan', collect());

        $hasilLaki = $bayiLakiLaki->map(function ($bayi) {
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
            $jenis = '';

            if ($bayi['umur_dalam_bulan'] >= 0 && $bayi['umur_dalam_bulan'] <= 60) {
                $umurBulan = $bayi['umur_dalam_bulan'];

                if (!is_null($umurBulan) && isset($median[$umurBulan])) {
                    if ($bayi['berat_badan'] > $median[$umurBulan]) {
                        $hitungatas = $bayi['berat_badan'] - $median[$umurBulan];
                        $hitungbawah = $plus1sd[$umurBulan] - $median[$umurBulan];
                        $bbu = $hitungatas / $hitungbawah;
                    } else {
                        $hitungatas = $bayi['berat_badan'] - $median[$umurBulan];
                        $hitungbawah = $median[$umurBulan] - $minus1sd[$umurBulan];
                        $bbu = $hitungatas / $hitungbawah;
                    }
                    if ($bbu < -3) {
                        $jenis = 'Berat Badan Sangat Kurang';
                    } elseif ($bbu >= -3 && $bbu < -2) {
                        $jenis = 'Berat Badan Kurang';
                    } elseif ($bbu >= -2 && $bbu <= 1) {
                        $jenis = 'Berat Badan Normal';
                    } elseif ($bbu > 1) {
                        $jenis = 'Resiko Berat Badan Lebih';
                    }
                }
            }

            return [
                'jenis_kelamin' => $bayi['jenis_kelamin'],
                'hitungatas' => $hitungatas,
                'hitungbawah' => $hitungbawah,
                'id' => $bayi['id'],
                'nama' => $bayi['nama'],
                'umur_dalam_bulan' => $bayi['umur_dalam_bulan'],
                'berat_badan' => $bayi['berat_badan'],
                'bbu' => $bbu,
                'jenis' => $jenis,
            ];
        });


        $hasilPerempuan = $bayiPerempuan->map(function ($bayi) {
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
            $jenis = '';


            if ($bayi['umur_dalam_bulan'] >= 0 && $bayi['umur_dalam_bulan'] <= 60) {
                $umurBulan = $bayi['umur_dalam_bulan'];

                if (!is_null($umurBulan) && isset($median[$umurBulan])) {
                    if ($bayi['berat_badan'] > $median[$umurBulan]) {
                        $hitungatas = $bayi['berat_badan'] - $median[$umurBulan];
                        $hitungbawah = $plus1sd[$umurBulan] - $median[$umurBulan];
                        $bbu = $hitungatas / $hitungbawah;
                    } else {
                        $hitungatas = $bayi['berat_badan'] - $median[$umurBulan];
                        $hitungbawah = $median[$umurBulan] - $minus1sd[$umurBulan];
                        $bbu = $hitungatas / $hitungbawah;
                    }
                    if ($bbu < -3) {
                        $jenis = 'Berat Badan Sangat Kurang';
                    } elseif ($bbu >= -3 && $bbu < -2) {
                        $jenis = 'Berat Badan Kurang';
                    } elseif ($bbu >= -2 && $bbu <= 1) {
                        $jenis = 'Berat Badan Normal';
                    } elseif ($bbu > 1) {
                        $jenis = 'Resiko Berat Badan Lebih';
                    }
                }
            }

            return [
                'id' => $bayi['id'],
                'nama' => $bayi['nama'],
                'umur_dalam_bulan' => $bayi['umur_dalam_bulan'],
                'berat_badan' => $bayi['berat_badan'],
                'bbu' => $bbu,
                'jenis' => $jenis,
                'jenis_kelamin' => $bayi['jenis_kelamin']
            ];
        });



        return response()->json(array_merge($hasilLaki->toArray(), $hasilPerempuan->toArray()));
    }

    public function gettinggibadanumur()
    {
        $bayis = Bayi::with(['PeriksaBalita' => function ($query) {
            $query->join('kegiatan_posyandus', 'periksa_balitas.kegiatanposyandu_balita_id', '=', 'kegiatan_posyandus.id')
                ->whereHas('Kegiatanposyandu', function ($subQuery) {
                    $subQuery->where('status_kegiatan', 'selesai');
                })
                ->orderByRaw('STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y") DESC');
        }])->get();


        $result = $bayis->map(function ($bayi) {
            $latestPeriksa = $bayi->PeriksaBalita->first();
            $tglKegiatan = $latestPeriksa ? $latestPeriksa->tgl_kegiatan : null;

            $umurDalamBulan = $tglKegiatan ? $this->getUmurDalamBulan($bayi->tanggal_lahir, $tglKegiatan) : null;

            return [
                'id' => $bayi->id,
                'nama' => $bayi->namabalita,
                'umur_dalam_bulan' => $umurDalamBulan,
                'jenis_kelamin' => $bayi->jenis_kelamin,
                'panjang_badan' => $latestPeriksa ? $latestPeriksa->panjang_badan : null,
            ];
        });

        $bayiByGender = $result->groupBy('jenis_kelamin');

        $bayiLakiLaki = $bayiByGender->get('Laki-laki', collect());
        $bayiPerempuan = $bayiByGender->get('Perempuan', collect());

        $hasilLaki = $bayiLakiLaki->map(function ($bayi) {
            $median = [
                49.9,
                54.7,
                58.4,
                61.4,
                63.9,
                65.9,
                67.6,
                69.2,
                70.6,
                72,
                73.3,
                74.5,
                75.7,
                76.9,
                78,
                79.1,
                80.2,
                81.2,
                82.3,
                83.2,
                84.2,
                85.1,
                86,
                86.9,
                87.8,
                87.1,
                88,
                88.8,
                89.6,
                90.4,
                91.2,
                91.9,
                92.7,
                93.4,
                94.1,
                94.8,
                95.4,
                96.1,
                96.7,
                97.4,
                98,
                98.6,
                99.2,
                99.9,
                100.4,
                101,
                101.6,
                102.2,
                102.8,
                103.3,
                103.9,
                104.4,
                105,
                105.6,
                106.1,
                106.7,
                107.2,
                107.8,
                108.3,
                108.9,
                109.4,
                110
            ];
            $plus1sd = [
                51.8,
                56.7,
                60.4,
                63.5,
                66,
                68,
                69.8,
                71.3,
                72.8,
                74.2,
                75.6,
                76.9,
                78.1,
                79.3,
                80.5,
                81.7,
                82.8,
                83.9,
                85,
                86,
                87,
                88,
                89,
                89.9,
                90.9,
                90.2,
                91.1,
                92,
                92.9,
                93.7,
                94.5,
                95.3,
                96.1,
                96.9,
                97.6,
                98.4,
                99.1,
                99.8,
                100.5,
                101.2,
                101.8,
                102.5,
                103.2,
                103.8,
                104.5,
                105.1,
                105.7,
                106.3,
                106.9,
                107.5,
                108.1,
                108.7,
                109.3,
                109.9,
                110.5,
                111.1,
                111.7,
                112.3,
                112.8,
                113.4,
                114,
                114.6
            ];
            $minus1sd = [
                48,
                52.8,
                56.4,
                59.4,
                61.8,
                63.8,
                65.5,
                67,
                68.4,
                69.7,
                71,
                72.2,
                73.4,
                74.5,
                75.6,
                76.6,
                77.6,
                78.6,
                79.6,
                80.5,
                81.4,
                82.3,
                83.1,
                83.9,
                84.8,
                84.1,
                84.9,
                85.6,
                86.4,
                87.1,
                87.8,
                88.5,
                89.2,
                89.9,
                90.5,
                91.1,
                91.8,
                92.4,
                93,
                93.6,
                94.2,
                94.7,
                95.3,
                95.9,
                96.4,
                97,
                97.5,
                98.1,
                98.6,
                99.1,
                99.7,
                100.2,
                100.7,
                101.2,
                101.7,
                102.3,
                102.8,
                103.3,
                103.8,
                104.3,
                104.8,
                105.3
            ];

            $tbu = 0;
            $jenis = '';

            $umurBulan = $bayi['umur_dalam_bulan'];

            if (!is_null($umurBulan) && isset($median[$umurBulan])) {
                if ($bayi['panjang_badan'] > $median[$umurBulan]) {
                    $hitungatas = $bayi['panjang_badan'] - $median[$umurBulan];
                    $hitungbawah = $plus1sd[$umurBulan] - $median[$umurBulan];
                    $tbu = $hitungatas / $hitungbawah;
                } else {
                    $hitungatas = $bayi['panjang_badan'] - $median[$umurBulan];
                    $hitungbawah = $median[$umurBulan] - $minus1sd[$umurBulan];
                    $tbu = $hitungatas / $hitungbawah;
                }
                if ($tbu < -3) {
                    $jenis = 'Sangat Pendek';
                } elseif ($tbu >= -3 && $tbu < -2) {
                    $jenis = 'Pendek';
                } elseif ($tbu >= -2 && $tbu <= 3) {
                    $jenis = 'Normal';
                } elseif ($tbu > 3) {
                    $jenis = 'Tinggi';
                }
            }

            return [
                'id' => $bayi['id'],
                'nama' => $bayi['nama'],
                'umur_dalam_bulan' => $bayi['umur_dalam_bulan'],
                'panjang_badan' => $bayi['panjang_badan'],
                'tbu' => $tbu,
                'jenis' => $jenis,
                'jenis_kelamin' => $bayi['jenis_kelamin']
            ];
        });


        $hasilPerempuan = $bayiPerempuan->map(function ($bayi) {
            $minus1sd = [
                47.3,
                51.7,
                55,
                57.7,
                59.9,
                61.8,
                63.5,
                65,
                66.4,
                67.7,
                69,
                70.3,
                71.4,
                72.6,
                73.7,
                74.8,
                75.8,
                76.8,
                77.8,
                78.8,
                79.7,
                80.6,
                81.5,
                82.3,
                83.2,
                82.5,
                83.3,
                84.1,
                84.9,
                85.7,
                86.4,
                87.1,
                87.9,
                88.6,
                89.3,
                89.9,
                90.6,
                91.2,
                91.9,
                92.5,
                93.1,
                93.8,
                94.4,
                95,
                95.6,
                96.2,
                96.7,
                97.3,
                97.9,
                98.4,
                99,
                99.5,
                100.1,
                100.6,
                101.1,
                101.6,
                102.2,
                102.7,
                103.2,
                103.7,
                104.2,
                104.7
            ];
            $median = [
                49.1,
                53.7,
                57.1,
                59.8,
                62.1,
                64,
                65.7,
                67.3,
                68.7,
                70.1,
                71.5,
                72.8,
                74,
                75.2,
                76.4,
                77.5,
                78.6,
                79.7,
                80.7,
                81.7,
                82.7,
                83.7,
                84.6,
                85.5,
                86.4,
                85.7,
                86.6,
                87.4,
                88.3,
                89.1,
                89.9,
                90.7,
                91.4,
                92.2,
                92.9,
                93.6,
                94.4,
                95.1,
                95.7,
                96.4,
                97.1,
                97.7,
                98.4,
                99,
                99.7,
                100.3,
                100.9,
                101.5,
                102.1,
                102.7,
                103.3,
                103.9,
                104.5,
                105,
                105.6,
                106.2,
                106.7,
                107.3,
                107.8,
                108.4,
                108.9,
                109.4
            ];
            $plus1sd = [
                51,
                55.6,
                59.1,
                61.9,
                64.3,
                66.2,
                68,
                69.6,
                71.1,
                72.6,
                73.9,
                75.3,
                76.6,
                77.8,
                79.1,
                80.2,
                81.4,
                82.5,
                83.6,
                84.7,
                85.7,
                86.7,
                87.7,
                88.7,
                89.6,
                88.9,
                89.9,
                90.8,
                91.7,
                92.5,
                93.4,
                94.2,
                95,
                95.8,
                96.6,
                97.4,
                98.1,
                98.9,
                99.6,
                100.3,
                101,
                101.7,
                102.4,
                103.1,
                103.8,
                104.5,
                105.1,
                105.8,
                106.4,
                107,
                107.7,
                108.3,
                108.9,
                109.5,
                110.1,
                110.7,
                111.3,
                111.9,
                112.5,
                113,
                113.6,
                114.2
            ];

            $tbu = 0;
            $jenis = '';


            $umurBulan = $bayi['umur_dalam_bulan'];

            if (!is_null($umurBulan) && isset($median[$umurBulan])) {

                if ($bayi['panjang_badan'] > $median[$umurBulan]) {
                    $hitungatas = $bayi['panjang_badan'] - $median[$umurBulan];
                    $hitungbawah = $plus1sd[$umurBulan] - $median[$umurBulan];
                    $tbu = $hitungatas / $hitungbawah;
                } else {
                    $hitungatas = $bayi['panjang_badan'] - $median[$umurBulan];
                    $hitungbawah = $median[$umurBulan] - $minus1sd[$umurBulan];
                    $tbu = $hitungatas / $hitungbawah;
                }
                if ($tbu < -3) {
                    $jenis = 'Sangat Pendek';
                } elseif ($tbu >= -3 && $tbu < -2) {
                    $jenis = 'Pendek';
                } elseif ($tbu >= -2 && $tbu < 3) {
                    $jenis = 'Normal';
                } elseif ($tbu >= 3) {
                    $jenis = 'Tinggi';
                }
            }

            return [
                'id' => $bayi['id'],
                'nama' => $bayi['nama'],
                'umur_dalam_bulan' => $bayi['umur_dalam_bulan'],
                'panjang_badan' => $bayi['panjang_badan'],
                'tbu' => $tbu,
                'jenis' => $jenis,
                'jenis_kelamin' => $bayi['jenis_kelamin']
            ];
        });



        return response()->json(array_merge($hasilLaki->toArray(), $hasilPerempuan->toArray()));
    }
    public function gettinggibadanumurpos($id)
    {
        $bayis = Bayi::with(['PeriksaBalita' => function ($query) {
            $query->join('kegiatan_posyandus', 'periksa_balitas.kegiatanposyandu_balita_id', '=', 'kegiatan_posyandus.id')
                ->whereHas('Kegiatanposyandu', function ($subQuery) {
                    $subQuery->where('status_kegiatan', 'selesai');
                })
                ->orderByRaw('STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y") DESC');
        }])->where('posyandus_id', $id)->get();

        $result = $bayis->map(function ($bayi) {
            $latestPeriksa = $bayi->PeriksaBalita->first();
            $tglKegiatan = $latestPeriksa ? $latestPeriksa->tgl_kegiatan : null;

            $umurDalamBulan = $tglKegiatan ? $this->getUmurDalamBulan($bayi->tanggal_lahir, $tglKegiatan) : null;

            return [
                'id' => $bayi->id,
                'nama' => $bayi->namabalita,
                'umur_dalam_bulan' => $umurDalamBulan,
                'jenis_kelamin' => $bayi->jenis_kelamin,
                'panjang_badan' => $latestPeriksa ? $latestPeriksa->panjang_badan : null,
            ];
        });

        $bayiByGender = $result->groupBy('jenis_kelamin');

        $bayiLakiLaki = $bayiByGender->get('Laki-laki', collect());
        $bayiPerempuan = $bayiByGender->get('Perempuan', collect());

        $hasilLaki = $bayiLakiLaki->map(function ($bayi) {
            $median = [
                49.9,
                54.7,
                58.4,
                61.4,
                63.9,
                65.9,
                67.6,
                69.2,
                70.6,
                72,
                73.3,
                74.5,
                75.7,
                76.9,
                78,
                79.1,
                80.2,
                81.2,
                82.3,
                83.2,
                84.2,
                85.1,
                86,
                86.9,
                87.8,
                87.1,
                88,
                88.8,
                89.6,
                90.4,
                91.2,
                91.9,
                92.7,
                93.4,
                94.1,
                94.8,
                95.4,
                96.1,
                96.7,
                97.4,
                98,
                98.6,
                99.2,
                99.9,
                100.4,
                101,
                101.6,
                102.2,
                102.8,
                103.3,
                103.9,
                104.4,
                105,
                105.6,
                106.1,
                106.7,
                107.2,
                107.8,
                108.3,
                108.9,
                109.4,
                110
            ];
            $plus1sd = [
                51.8,
                56.7,
                60.4,
                63.5,
                66,
                68,
                69.8,
                71.3,
                72.8,
                74.2,
                75.6,
                76.9,
                78.1,
                79.3,
                80.5,
                81.7,
                82.8,
                83.9,
                85,
                86,
                87,
                88,
                89,
                89.9,
                90.9,
                90.2,
                91.1,
                92,
                92.9,
                93.7,
                94.5,
                95.3,
                96.1,
                96.9,
                97.6,
                98.4,
                99.1,
                99.8,
                100.5,
                101.2,
                101.8,
                102.5,
                103.2,
                103.8,
                104.5,
                105.1,
                105.7,
                106.3,
                106.9,
                107.5,
                108.1,
                108.7,
                109.3,
                109.9,
                110.5,
                111.1,
                111.7,
                112.3,
                112.8,
                113.4,
                114,
                114.6
            ];
            $minus1sd = [
                48,
                52.8,
                56.4,
                59.4,
                61.8,
                63.8,
                65.5,
                67,
                68.4,
                69.7,
                71,
                72.2,
                73.4,
                74.5,
                75.6,
                76.6,
                77.6,
                78.6,
                79.6,
                80.5,
                81.4,
                82.3,
                83.1,
                83.9,
                84.8,
                84.1,
                84.9,
                85.6,
                86.4,
                87.1,
                87.8,
                88.5,
                89.2,
                89.9,
                90.5,
                91.1,
                91.8,
                92.4,
                93,
                93.6,
                94.2,
                94.7,
                95.3,
                95.9,
                96.4,
                97,
                97.5,
                98.1,
                98.6,
                99.1,
                99.7,
                100.2,
                100.7,
                101.2,
                101.7,
                102.3,
                102.8,
                103.3,
                103.8,
                104.3,
                104.8,
                105.3
            ];

            $tbu = 0;
            $jenis = '';


            $umurBulan = $bayi['umur_dalam_bulan'];

            if (!is_null($umurBulan) && isset($median[$umurBulan])) {

                if ($bayi['panjang_badan'] > $median[$umurBulan]) {
                    $hitungatas = $bayi['panjang_badan'] - $median[$umurBulan];
                    $hitungbawah = $plus1sd[$umurBulan] - $median[$umurBulan];
                    $tbu = $hitungatas / $hitungbawah;
                } else {
                    $hitungatas = $bayi['panjang_badan'] - $median[$umurBulan];
                    $hitungbawah = $median[$umurBulan] - $minus1sd[$umurBulan];
                    $tbu = $hitungatas / $hitungbawah;
                }
                if ($tbu < -3) {
                    $jenis = 'Sangat Pendek';
                } elseif ($tbu >= -3 && $tbu < -2) {
                    $jenis = 'Pendek';
                } elseif ($tbu >= -2 && $tbu <= 3) {
                    $jenis = 'Normal';
                } elseif ($tbu > 3) {
                    $jenis = 'Tinggi';
                }
            }


            return [
                'id' => $bayi['id'],
                'nama' => $bayi['nama'],
                'umur_dalam_bulan' => $bayi['umur_dalam_bulan'],
                'panjang_badan' => $bayi['panjang_badan'],
                'tbu' => $tbu,
                'jenis' => $jenis,
                'jenis_kelamin' => $bayi['jenis_kelamin']
            ];
        });


        $hasilPerempuan = $bayiPerempuan->map(function ($bayi) {
            $minus1sd = [
                47.3,
                51.7,
                55,
                57.7,
                59.9,
                61.8,
                63.5,
                65,
                66.4,
                67.7,
                69,
                70.3,
                71.4,
                72.6,
                73.7,
                74.8,
                75.8,
                76.8,
                77.8,
                78.8,
                79.7,
                80.6,
                81.5,
                82.3,
                83.2,
                82.5,
                83.3,
                84.1,
                84.9,
                85.7,
                86.4,
                87.1,
                87.9,
                88.6,
                89.3,
                89.9,
                90.6,
                91.2,
                91.9,
                92.5,
                93.1,
                93.8,
                94.4,
                95,
                95.6,
                96.2,
                96.7,
                97.3,
                97.9,
                98.4,
                99,
                99.5,
                100.1,
                100.6,
                101.1,
                101.6,
                102.2,
                102.7,
                103.2,
                103.7,
                104.2,
                104.7
            ];
            $median = [
                49.1,
                53.7,
                57.1,
                59.8,
                62.1,
                64,
                65.7,
                67.3,
                68.7,
                70.1,
                71.5,
                72.8,
                74,
                75.2,
                76.4,
                77.5,
                78.6,
                79.7,
                80.7,
                81.7,
                82.7,
                83.7,
                84.6,
                85.5,
                86.4,
                85.7,
                86.6,
                87.4,
                88.3,
                89.1,
                89.9,
                90.7,
                91.4,
                92.2,
                92.9,
                93.6,
                94.4,
                95.1,
                95.7,
                96.4,
                97.1,
                97.7,
                98.4,
                99,
                99.7,
                100.3,
                100.9,
                101.5,
                102.1,
                102.7,
                103.3,
                103.9,
                104.5,
                105,
                105.6,
                106.2,
                106.7,
                107.3,
                107.8,
                108.4,
                108.9,
                109.4
            ];
            $plus1sd = [
                51,
                55.6,
                59.1,
                61.9,
                64.3,
                66.2,
                68,
                69.6,
                71.1,
                72.6,
                73.9,
                75.3,
                76.6,
                77.8,
                79.1,
                80.2,
                81.4,
                82.5,
                83.6,
                84.7,
                85.7,
                86.7,
                87.7,
                88.7,
                89.6,
                88.9,
                89.9,
                90.8,
                91.7,
                92.5,
                93.4,
                94.2,
                95,
                95.8,
                96.6,
                97.4,
                98.1,
                98.9,
                99.6,
                100.3,
                101,
                101.7,
                102.4,
                103.1,
                103.8,
                104.5,
                105.1,
                105.8,
                106.4,
                107,
                107.7,
                108.3,
                108.9,
                109.5,
                110.1,
                110.7,
                111.3,
                111.9,
                112.5,
                113,
                113.6,
                114.2
            ];

            $tbu = 0;
            $jenis = '';


            $umurBulan = $bayi['umur_dalam_bulan'];

            if (!is_null($umurBulan) && isset($median[$umurBulan])) {

                if ($bayi['panjang_badan'] > $median[$umurBulan]) {
                    $hitungatas = $bayi['panjang_badan'] - $median[$umurBulan];
                    $hitungbawah = $plus1sd[$umurBulan] - $median[$umurBulan];
                    $tbu = $hitungatas / $hitungbawah;
                } else {
                    $hitungatas = $bayi['panjang_badan'] - $median[$umurBulan];
                    $hitungbawah = $median[$umurBulan] - $minus1sd[$umurBulan];
                    $tbu = $hitungatas / $hitungbawah;
                }
                if ($tbu < -3) {
                    $jenis = 'Sangat Pendek';
                } elseif ($tbu >= -3 && $tbu < -2) {
                    $jenis = 'Pendek';
                } elseif ($tbu >= -2 && $tbu <= 3) {
                    $jenis = 'Normal';
                } elseif ($tbu > 3) {
                    $jenis = 'Tinggi';
                }
            }


            return [
                'id' => $bayi['id'],
                'nama' => $bayi['nama'],
                'umur_dalam_bulan' => $bayi['umur_dalam_bulan'],
                'panjang_badan' => $bayi['panjang_badan'],
                'tbu' => $tbu,
                'jenis' => $jenis,
                'jenis_kelamin' => $bayi['jenis_kelamin']
            ];
        });



        return response()->json(array_merge($hasilLaki->toArray(), $hasilPerempuan->toArray()));
    }

    public function getPeriksaLast12Monthsbalita()
    {
        $currentDate = now();
        $monthsNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthIndex = (int)$date->format('m') - 1;

            $months[$monthKey] = [
                'month' => $monthsNames[$monthIndex],
                'panjang_badan' => null,
                'berat_badan' => null,
                'lingkep_periksa' => null,
            ];
        }

        $data = PeriksaBalita::selectRaw('
            DATE_FORMAT(STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y"), "%Y-%m") as month,
            AVG(panjang_badan) as panjang_badan,
            AVG(berat_badan) as berat_badan,
            AVG(lingkep_periksa) as lingkep_periksa
        ')
            ->join('kegiatan_posyandus', 'periksa_balitas.kegiatanposyandu_balita_id', '=', 'kegiatan_posyandus.id')
            ->whereRaw('STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y") >= ?', [$currentDate->subMonths(12)->format('Y-m-d')])
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        foreach ($data as $entry) {
            if (isset($months[$entry->month])) {
                $months[$entry->month]['panjang_badan'] = round($entry->panjang_badan, 2);
                $months[$entry->month]['berat_badan'] = round($entry->berat_badan, 2);
                $months[$entry->month]['lingkep_periksa'] = round($entry->lingkep_periksa, 2);
            }
        }


        return response()->json(array_values($months));
    }
    public function getPeriksaLast12MonthsbalitaPOS($id)
    {
        $currentDate = now();
        $monthsNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthIndex = (int)$date->format('m') - 1;

            $months[$monthKey] = [
                'month' => $monthsNames[$monthIndex],
                'panjang_badan' => null,
                'berat_badan' => null,
                'lingkep_periksa' => null,
            ];
        }

        $data = PeriksaBalita::selectRaw('
            DATE_FORMAT(STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y"), "%Y-%m") as month,
            AVG(panjang_badan) as panjang_badan,
            AVG(berat_badan) as berat_badan,
            AVG(lingkep_periksa) as lingkep_periksa
        ')
            ->join('kegiatan_posyandus', 'periksa_balitas.kegiatanposyandu_balita_id', '=', 'kegiatan_posyandus.id')
            ->whereRaw('STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y") >= ?', [$currentDate->subMonths(12)->format('Y-m-d')])
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->where('kegiatan_posyandus.posyandu_id', $id)
            ->get();

        foreach ($data as $entry) {
            if (isset($months[$entry->month])) {
                $months[$entry->month]['panjang_badan'] = round($entry->panjang_badan, 2);
                $months[$entry->month]['berat_badan'] = round($entry->berat_badan, 2);
                $months[$entry->month]['lingkep_periksa'] = round($entry->lingkep_periksa, 2);
            }
        }


        return response()->json(array_values($months));
    }


    public function getberatbadantinggi()
    {
        $bayis = Bayi::with(['PeriksaBalita' => function ($query) {
            $query->join('kegiatan_posyandus', 'periksa_balitas.kegiatanposyandu_balita_id', '=', 'kegiatan_posyandus.id')
                ->whereHas('Kegiatanposyandu', function ($subQuery) {
                    $subQuery->where('status_kegiatan', 'selesai');
                })
                ->orderByRaw('STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y") DESC');
        }])->get();

        $result = $bayis->map(function ($bayi) {
            $latestPeriksa = $bayi->PeriksaBalita->first();
            $tglKegiatan = $latestPeriksa ? $latestPeriksa->tgl_kegiatan : null;

            $umurDalamBulan = $tglKegiatan ? $this->getUmurDalamBulan($bayi->tanggal_lahir, $tglKegiatan) : null;

            return [
                'id' => $bayi->id,
                'nama' => $bayi->namabalita,
                'umur_dalam_bulan' => $umurDalamBulan,
                'jenis_kelamin' => $bayi->jenis_kelamin,
                'berat_badan' => $latestPeriksa ? $latestPeriksa->berat_badan : null,
                'panjang_badan' => $latestPeriksa ? $latestPeriksa->panjang_badan : null,
            ];
        });

        $bayiByGender = $result->groupBy('jenis_kelamin');

        $bayiLakiLaki = $bayiByGender->get('Laki-laki', collect());
        $bayiPerempuan = $bayiByGender->get('Perempuan', collect());

        $hasilLaki = $bayiLakiLaki->map(function ($bayi) {

            $bbu = 0;
            $median = null;
            $hitungatas = null;
            $hitungbawah = null;
            $bbu = null;
            $jenis = 'Tidak Diketahui';

            if ($bayi['umur_dalam_bulan'] >= 0 && $bayi['umur_dalam_bulan'] < 24) {
                $umurBulan = $bayi['umur_dalam_bulan'];
                $acuan = [
                    [45, 2.2, 2.4, 2.7],
                    [45.5, 2.3, 2.5, 2.8],
                    [46, 2.4, 2.6, 2.9],
                    [46.5, 2.5, 2.7, 3],
                    [47, 2.5, 2.8, 3],
                    [47.5, 2.6, 2.9, 3.1],
                    [48, 2.7, 2.9, 3.2],
                    [48.5, 2.8, 3, 3.3],
                    [49, 2.9, 3.1, 3.4],
                    [49.5, 3, 3.2, 3.5],
                    [50, 3, 3.3, 3.6],
                    [50.5, 3.1, 3.4, 3.8],
                    [51, 3.2, 3.5, 3.9],
                    [51.5, 3.3, 3.6, 4],
                    [52, 3.5, 3.8, 4.1],
                    [52.5, 3.6, 3.9, 4.2],
                    [53, 3.7, 4, 4.4],
                    [53.5, 3.8, 4.1, 4.5],
                    [54, 3.9, 4.3, 4.7],
                    [54.5, 4, 4.4, 4.8],
                    [55, 4.2, 4.5, 5],
                    [55.5, 4.3, 4.7, 5.1],
                    [56, 4.4, 4.8, 5.3],
                    [56.5, 4.6, 5, 5.4],
                    [57, 4.7, 5.1, 5.6],
                    [57.5, 4.9, 5.3, 5.7],
                    [58, 5, 5.4, 5.9],
                    [58.5, 5.1, 5.6, 6.1],
                    [59, 5.3, 5.7, 6.2],
                    [59.5, 5.4, 5.9, 6.4],
                    [60, 5.5, 6, 6.5],
                    [60.5, 5.6, 6.1, 6.7],
                    [61, 5.8, 6.3, 6.8],
                    [61.5, 5.9, 6.4, 7],
                    [62, 6, 6.5, 7.1],
                    [62.5, 6.1, 6.7, 7.2],
                    [63, 6.2, 6.8, 7.4],
                    [63.5, 6.4, 6.9, 7.5],
                    [64, 6.5, 7, 7.6],
                    [64.5, 6.6, 7.1, 7.8],
                    [65, 6.7, 7.3, 7.9],
                    [65.5, 6.8, 7.4, 8],
                    [66, 6.9, 7.5, 8.2],
                    [66.5, 7, 7.6, 8.3],
                    [67, 7.1, 7.7, 8.4],
                    [67.5, 7.2, 7.9, 8.5],
                    [68, 7.3, 8, 8.7],
                    [68.5, 7.5, 8.1, 8.8],
                    [69, 7.6, 8.2, 8.9],
                    [69.5, 7.7, 8.3, 9],
                    [70, 7.8, 8.4, 9.2],
                    [70.5, 7.9, 8.5, 9.3],
                    [71, 8, 8.6, 9.4],
                    [71.5, 8.1, 8.8, 9.5],
                    [72, 8.2, 8.9, 9.6],
                    [72.5, 8.3, 9, 9.8],
                    [73, 8.4, 9.1, 9.9],
                    [73.5, 8.5, 9.2, 10],
                    [74, 8.6, 9.3, 10.1],
                    [74.5, 8.7, 9.4, 10.2],
                    [75, 8.8, 9.5, 10.3],
                    [75.5, 8.8, 9.6, 10.4],
                    [76, 8.9, 9.7, 10.6],
                    [76.5, 9, 9.8, 10.7],
                    [77, 9.1, 9.9, 10.8],
                    [77.5, 9.2, 10, 10.9],
                    [78, 9.3, 10.1, 11],
                    [78.5, 9.4, 10.2, 11.1],
                    [79, 9.5, 10.3, 11.2],
                    [79.5, 9.5, 10.4, 11.3],
                    [80, 9.6, 10.4, 11.4],
                    [80.5, 9.7, 10.5, 11.5],
                    [81, 9.8, 10.6, 11.6],
                    [81.5, 9.9, 10.7, 11.7],
                    [82, 10, 10.8, 11.8],
                    [82.5, 10.1, 10.9, 11.9],
                    [83, 10.2, 11, 12],
                    [83.5, 10.3, 11.2, 12.1],
                    [84, 10.4, 11.3, 12.2],
                    [84.5, 10.5, 11.4, 12.4],
                    [85, 10.6, 11.5, 12.5],
                    [85.5, 10.7, 11.6, 12.6],
                    [86, 10.8, 11.7, 12.8],
                    [86.5, 11, 11.9, 12.9],
                    [87, 11.1, 12, 13],
                    [87.5, 11.2, 12.1, 13.2],
                    [88, 11.3, 12.2, 13.3],
                    [88.5, 11.4, 12.4, 13.4],
                    [89, 11.5, 12.5, 13.5],
                    [89.5, 11.6, 12.6, 13.7],
                    [90, 11.8, 12.7, 13.8],
                    [90.5, 11.9, 12.8, 13.9],
                    [91, 12, 13, 14.1],
                    [91.5, 12.1, 13.1, 14.2],
                    [92, 12.2, 13.2, 14.3],
                    [92.5, 12.3, 13.3, 14.4],
                    [93, 12.4, 13.4, 14.6],
                    [93.5, 12.5, 13.5, 14.7],
                    [94, 12.6, 13.7, 14.8],
                    [94.5, 12.7, 13.8, 14.9],
                    [95, 12.8, 13.9, 15.1],
                    [95.5, 12.9, 14, 15.2],
                    [96, 13.1, 14.1, 15.3],
                    [96.5, 13.2, 14.3, 15.5],
                    [97, 13.3, 14.4, 15.6],
                    [97.5, 13.4, 14.5, 15.7],
                    [98, 13.5, 14.6, 15.9],
                    [98.5, 13.6, 14.8, 16],
                    [99, 13.7, 14.9, 16.2],
                    [99.5, 13.9, 15, 16.3],
                    [100, 14, 15.2, 16.5],
                    [100.5, 14.1, 15.3, 16.6],
                    [101, 14.2, 15.4, 16.8],
                    [101.5, 14.4, 15.6, 16.9],
                    [102, 14.5, 15.7, 17.1],
                    [102.5, 14.6, 15.9, 17.3],
                    [103, 14.8, 16, 17.4],
                    [103.5, 14.9, 16.2, 17.6],
                    [104, 15, 16.3, 17.8],
                    [104.5, 15.2, 16.5, 17.9],
                    [105, 15.3, 16.6, 18.1],
                    [105.5, 15.4, 16.8, 18.3],
                    [106, 15.6, 16.9, 18.5],
                    [106.5, 15.7, 17.1, 18.6],
                    [107, 15.9, 17.3, 18.8],
                    [107.5, 16, 17.4, 19],
                    [108, 16.2, 17.6, 19.2],
                    [108.5, 16.3, 17.8, 19.4],
                    [109, 16.5, 17.9, 19.6],
                    [109.5, 16.6, 18.1, 19.8],
                    [110, 16.8, 18.3, 20],
                ];
            } elseif ($bayi['umur_dalam_bulan'] >= 24 && $bayi['umur_dalam_bulan'] <= 60) {
                $umurBulan = $bayi['umur_dalam_bulan'];
                $acuan = [
                    [65, 6.9, 7.4, 8.1],
                    [65.5, 7, 7.6, 8.2],
                    [66, 7.1, 7.7, 8.3],
                    [66.5, 7.2, 7.8, 8.5],
                    [67, 7.3, 7.9, 8.6],
                    [67.5, 7.4, 8, 8.7],
                    [68, 7.5, 8.1, 8.8],
                    [68.5, 7.6, 8.2, 9],
                    [69, 7.7, 8.4, 9.1],
                    [69.5, 7.8, 8.5, 9.2],
                    [70, 7.9, 8.6, 9.3],
                    [70.5, 8, 8.7, 9.5],
                    [71, 8.1, 8.8, 9.6],
                    [71.5, 8.2, 8.9, 9.7],
                    [72, 8.3, 9, 9.8],
                    [72.5, 8.4, 9.1, 9.9],
                    [73, 8.5, 9.2, 10],
                    [73.5, 8.6, 9.3, 10.2],
                    [74, 8.7, 9.4, 10.3],
                    [74.5, 8.8, 9.5, 10.4],
                    [75, 8.9, 9.6, 10.5],
                    [75.5, 9, 9.7, 10.6],
                    [76, 9.1, 9.8, 10.7],
                    [76.5, 9.2, 9.9, 10.8],
                    [77, 9.2, 10, 10.9],
                    [77.5, 9.3, 10.1, 11],
                    [78, 9.4, 10.2, 11.1],
                    [78.5, 9.5, 10.3, 11.2],
                    [79, 9.6, 10.4, 11.3],
                    [79.5, 9.7, 10.5, 11.4],
                    [80, 9.7, 10.6, 11.5],
                    [80.5, 9.8, 10.7, 11.6],
                    [81, 9.9, 10.8, 11.7],
                    [81.5, 10, 10.9, 11.8],
                    [82, 10.1, 11, 11.9],
                    [82.5, 10.2, 11.1, 12.1],
                    [83, 10.3, 11.2, 12.2],
                    [83.5, 10.4, 11.3, 12.3],
                    [84, 10.5, 11.4, 12.4],
                    [84.5, 10.7, 11.5, 12.5],
                    [85, 10.8, 11.7, 12.7],
                    [85.5, 10.9, 11.8, 12.8],
                    [86, 11, 11.9, 12.9],
                    [86.5, 11.1, 12, 13.1],
                    [87, 11.2, 12.2, 13.2],
                    [87.5, 11.3, 12.3, 13.3],
                    [88, 11.5, 12.4, 13.5],
                    [88.5, 11.6, 12.5, 13.6],
                    [89, 11.7, 12.6, 13.7],
                    [89.5, 11.8, 12.8, 13.9],
                    [90, 11.9, 12.9, 14],
                    [90.5, 12, 13, 14.1],
                    [91, 12.1, 13.1, 14.2],
                    [91.5, 12.2, 13.2, 14.4],
                    [92, 12.3, 13.4, 14.5],
                    [92.5, 12.4, 13.5, 14.6],
                    [93, 12.6, 13.6, 14.7],
                    [93.5, 12.7, 13.7, 14.9],
                    [94, 12.8, 13.8, 15],
                    [94.5, 12.9, 13.9, 15.1],
                    [95, 13, 14.1, 15.3],
                    [95.5, 13.1, 14.2, 15.4],
                    [96, 13.2, 14.3, 15.5],
                    [96.5, 13.3, 14.4, 15.7],
                    [97, 13.4, 14.6, 15.8],
                    [97.5, 13.6, 14.7, 15.9],
                    [98, 13.7, 14.8, 16.1],
                    [98.5, 13.8, 14.9, 16.2],
                    [99, 13.9, 15.1, 16.4],
                    [99.5, 14, 15.2, 16.5],
                    [100, 14.2, 15.4, 16.7],
                    [100.5, 14.3, 15.5, 16.9],
                    [101, 14.4, 15.6, 17],
                    [101.5, 14.5, 15.8, 17.2],
                    [102, 14.7, 15.9, 17.3],
                    [102.5, 14.8, 16.1, 17.5],
                    [103, 14.9, 16.2, 17.7],
                    [103.5, 15.1, 16.4, 17.8],
                    [104, 15.2, 16.5, 18],
                    [104.5, 15.4, 16.7, 18.2],
                    [105, 15.5, 16.8, 18.4],
                    [105.5, 15.6, 17, 18.5],
                    [106, 15.8, 17.2, 18.7],
                    [106.5, 15.9, 17.3, 18.9],
                    [107, 16.1, 17.5, 19.1],
                    [107.5, 16.2, 17.7, 19.3],
                    [108, 16.4, 17.8, 19.5],
                    [108.5, 16.5, 18, 19.7],
                    [109, 16.7, 18.2, 19.8],
                    [109.5, 16.8, 18.3, 20],
                    [110, 17, 18.5, 20.2],
                    [110.5, 17.1, 18.7, 20.4],
                    [111, 17.3, 18.9, 20.7],
                    [111.5, 17.5, 19.1, 20.9],
                    [112, 17.6, 19.2, 21.1],
                    [112.5, 17.8, 19.4, 21.3],
                    [113, 18, 19.6, 21.5],
                    [113.5, 18.1, 19.8, 21.7],
                    [114, 18.3, 20, 21.9],
                    [114.5, 18.5, 20.2, 22.1],
                    [115, 18.6, 20.4, 22.4],
                    [115.5, 18.8, 20.6, 22.6],
                    [116, 19, 20.8, 22.8],
                    [116.5, 19.2, 21, 23],
                    [117, 19.3, 21.2, 23.3],
                    [117.5, 19.5, 21.4, 23.5],
                    [118, 19.7, 21.6, 23.7],
                    [118.5, 19.9, 21.8, 23.9],
                    [119, 20, 22, 24.1],
                    [119.5, 20.2, 22.2, 24.4],
                    [120, 20.4, 22.4, 24.6],
                ];
            }

            $tolerance = 0.5;

            $closestRow = null;
            $closestDifference = PHP_FLOAT_MAX;

            foreach ($acuan as $row) {
                $difference = abs($row[0] - $bayi['panjang_badan']);
                if ($difference <= $tolerance && $difference < $closestDifference) {
                    $closestRow = $row;
                    $closestDifference = $difference;
                }
            }
            if ($closestRow) {
                list($A_closest, $B, $C, $D) = $closestRow;

                if ($bayi['berat_badan'] > $C) {
                    $median = $C;
                    $hitungatas = $bayi['berat_badan'] - $C;
                    $hitungbawah = $D - $C;
                    $bbu = $hitungatas / $hitungbawah;
                } else {
                    $median = $C;
                    $hitungatas = $bayi['berat_badan'] - $C;
                    $hitungbawah = $C - $B;
                    $bbu = $hitungatas / $hitungbawah;
                }
            }


            if ($bbu !== null) {
                if ($bbu < -3) {
                    $jenis = 'Gizi Buruk';
                } elseif ($bbu >= -3 && $bbu < -2) {
                    $jenis = 'Gizi Kurang';
                } elseif ($bbu >= -2 && $bbu <= 1) {
                    $jenis = 'Gizi Baik';
                } elseif ($bbu > 1 && $bbu <= 2) {
                    $jenis = 'Beresiko Gizi Lebih';
                } elseif ($bbu > 2 && $bbu <= 3) {
                    $jenis = 'Gizi Lebih';
                } elseif ($bbu > 3) {
                    $jenis = 'Obesitas';
                }
            }

            return [
                'jenis_kelamin' => $bayi['jenis_kelamin'],
                'hitungatas' => $hitungatas,
                'hitungbawah' => $hitungbawah,
                'id' => $bayi['id'],
                'nama' => $bayi['nama'],
                'umur_dalam_bulan' => $bayi['umur_dalam_bulan'],
                'berat_badan' => $bayi['berat_badan'],
                'panjang_badan' => $bayi['panjang_badan'],
                'bbu' => $bbu,
                'jenis' => $jenis,
                'median' => $median

            ];
        });


        $hasilPerempuan = $bayiPerempuan->map(function ($bayi) {
            $bbu = 0;
            $median = null;
            $hitungatas = null;
            $hitungbawah = null;
            $bbu = null;
            $jenis = 'Tidak Diketahui';

            if ($bayi['umur_dalam_bulan'] >= 0 && $bayi['umur_dalam_bulan'] < 24) {
                $umurBulan = $bayi['umur_dalam_bulan'];
                $acuan = [
                    [45, 2.3, 2.5, 2.7],
                    [45.5, 2.3, 2.5, 2.8],
                    [46, 2.4, 2.6, 2.9],
                    [46.5, 2.5, 2.7, 3],
                    [47, 2.6, 2.8, 3.1],
                    [47.5, 2.6, 2.9, 3.2],
                    [48, 2.7, 3, 3.3],
                    [48.5, 2.8, 3.1, 3.4],
                    [49, 2.9, 3.2, 3.5],
                    [49.5, 3, 3.3, 3.6],
                    [50, 3.1, 3.4, 3.7],
                    [50.5, 3.2, 3.5, 3.8],
                    [51, 3.3, 3.6, 3.9],
                    [51.5, 3.4, 3.7, 4],
                    [52, 3.5, 3.8, 4.2],
                    [52.5, 3.6, 3.9, 4.3],
                    [53, 3.7, 4, 4.4],
                    [53.5, 3.8, 4.2, 4.6],
                    [54, 3.9, 4.3, 4.7],
                    [54.5, 4, 4.4, 4.8],
                    [55, 4.2, 4.5, 5],
                    [55.5, 4.3, 4.7, 5.1],
                    [56, 4.4, 4.8, 5.3],
                    [56.5, 4.5, 5, 5.4],
                    [57, 4.6, 5.1, 5.6],
                    [57.5, 4.8, 5.2, 5.7],
                    [58, 4.9, 5.4, 5.9],
                    [58.5, 5, 5.5, 6],
                    [59, 5.1, 5.6, 6.2],
                    [59.5, 5.3, 5.7, 6.3],
                    [60, 5.4, 5.9, 6.4],
                    [60.5, 5.5, 6, 6.6],
                    [61, 5.6, 6.1, 6.7],
                    [61.5, 5.7, 6.3, 6.9],
                    [62, 5.8, 6.4, 7],
                    [62.5, 5.9, 6.5, 7.1],
                    [63, 6, 6.6, 7.3],
                    [63.5, 6.2, 6.7, 7.4],
                    [64, 6.3, 6.9, 7.5],
                    [64.5, 6.4, 7, 7.6],
                    [65, 6.5, 7.1, 7.8],
                    [65.5, 6.6, 7.2, 7.9],
                    [66, 6.7, 7.3, 8],
                    [66.5, 6.8, 7.4, 8.1],
                    [67, 6.9, 7.5, 8.3],
                    [67.5, 7, 7.6, 8.4],
                    [68, 7.1, 7.7, 8.5],
                    [68.5, 7.2, 7.9, 8.6],
                    [69, 7.3, 8, 8.7],
                    [69.5, 7.4, 8.1, 8.8],
                    [70, 7.5, 8.2, 9],
                    [70.5, 7.6, 8.3, 9.1],
                    [71, 7.7, 8.4, 9.2],
                    [71.5, 7.7, 8.5, 9.3],
                    [72, 7.8, 8.6, 9.4],
                    [72.5, 7.9, 8.7, 9.5],
                    [73, 8, 8.8, 9.6],
                    [73.5, 8.1, 8.9, 9.7],
                    [74, 8.2, 9, 9.8],
                    [74.5, 8.3, 9.1, 9.9],
                    [75, 8.4, 9.1, 10],
                    [75.5, 8.5, 9.2, 10.1],
                    [76, 8.5, 9.3, 10.2],
                    [76.5, 8.6, 9.4, 10.3],
                    [77, 8.7, 9.5, 10.4],
                    [77.5, 8.8, 9.6, 10.5],
                    [78, 8.9, 9.7, 10.6],
                    [78.5, 9, 9.8, 10.7],
                    [79, 9.1, 9.9, 10.8],
                    [79.5, 9.1, 10, 10.9],
                    [80, 9.2, 10.1, 11],
                    [80.5, 9.3, 10.2, 11.2],
                    [81, 9.4, 10.3, 11.3],
                    [81.5, 9.5, 10.4, 11.4],
                    [82, 9.6, 10.5, 11.5],
                    [82.5, 9.7, 10.6, 11.6],
                    [83, 9.8, 10.7, 11.8],
                    [83.5, 9.9, 10.9, 11.9],
                    [84, 10.1, 11, 12],
                    [84.5, 10.2, 11.1, 12.1],
                    [85, 10.3, 11.2, 12.3],
                    [85.5, 10.4, 11.3, 12.4],
                    [86, 10.5, 11.5, 12.6],
                    [86.5, 10.6, 11.6, 12.7],
                    [87, 10.7, 11.7, 12.8],
                    [87.5, 10.9, 11.8, 13],
                    [88, 11, 12, 13.1],
                    [88.5, 11.1, 12.1, 13.2],
                    [89, 11.2, 12.2, 13.4],
                    [89.5, 11.3, 12.3, 13.5],
                    [90, 11.4, 12.5, 13.7],
                    [90.5, 11.5, 12.6, 13.8],
                    [91, 11.7, 12.7, 13.9],
                    [91.5, 11.8, 12.8, 14.1],
                    [92, 11.9, 13, 14.2],
                    [92.5, 12, 13.1, 14.3],
                    [93, 12.1, 13.2, 14.5],
                    [93.5, 12.2, 13.3, 14.6],
                    [94, 12.3, 13.5, 14.7],
                    [94.5, 12.4, 13.6, 14.9],
                    [95, 12.6, 13.7, 15],
                    [95.5, 12.7, 13.8, 15.2],
                    [96, 12.8, 14, 15.3],
                    [96.5, 12.9, 14.1, 15.4],
                    [97, 13, 14.2, 15.6],
                    [97.5, 13.1, 14.4, 15.7],
                    [98, 13.3, 14.5, 15.9],
                    [98.5, 13.4, 14.6, 16],
                    [99, 13.5, 14.8, 16.2],
                    [99.5, 13.6, 14.9, 16.3],
                    [100, 13.7, 15, 16.5],
                    [100.5, 13.9, 15.2, 16.6],
                    [101, 14, 15.3, 16.8],
                    [101.5, 14.1, 15.5, 17],
                    [102, 14.3, 15.6, 17.1],
                    [102.5, 14.4, 15.8, 17.3],
                    [103, 14.5, 15.9, 17.5],
                    [103.5, 14.7, 16.1, 17.6],
                    [104, 14.8, 16.2, 17.8],
                    [104.5, 15, 16.4, 18],
                    [105, 15.1, 16.5, 18.2],
                    [105.5, 15.3, 16.7, 18.4],
                    [106, 15.4, 16.9, 18.5],
                    [106.5, 15.6, 17.1, 18.7],
                    [107, 15.7, 17.2, 18.9],
                    [107.5, 15.9, 17.4, 19.1],
                    [108, 16, 17.6, 19.3],
                    [108.5, 16.2, 17.8, 19.5],
                    [109, 16.4, 18, 19.7],
                    [109.5, 16.5, 18.1, 20],
                    [110, 16.7, 18.3, 20.2],
                ];
            } elseif ($bayi['umur_dalam_bulan'] >= 24 && $bayi['umur_dalam_bulan'] <= 60) {
                $umurBulan = $bayi['umur_dalam_bulan'];
                $acuan = [
                    [65, 6.6, 7.2, 7.9],
                    [65.5, 6.7, 7.4, 8.1],
                    [66, 6.8, 7.5, 8.2],
                    [66.5, 6.9, 7.6, 8.3],
                    [67, 7.0, 7.7, 8.4],
                    [67.5, 7.1, 7.8, 8.5],
                    [68, 7.2, 7.9, 8.7],
                    [68.5, 7.3, 8.0, 8.8],
                    [69, 7.4, 8.1, 8.9],
                    [69.5, 7.5, 8.2, 9.0],
                    [70, 7.6, 8.3, 9.1],
                    [70.5, 7.7, 8.4, 9.2],
                    [71, 7.8, 8.5, 9.3],
                    [71.5, 7.9, 8.6, 9.4],
                    [72, 8.0, 8.7, 9.5],
                    [72.5, 8.1, 8.8, 9.7],
                    [73, 8.1, 8.9, 9.8],
                    [73.5, 8.2, 9.0, 9.9],
                    [74, 8.3, 9.1, 10.0],
                    [74.5, 8.4, 9.2, 10.1],
                    [75, 8.5, 9.3, 10.2],
                    [75.5, 8.6, 9.4, 10.3],
                    [76, 8.7, 9.5, 10.4],
                    [76.5, 8.7, 9.6, 10.5],
                    [77, 8.8, 9.6, 10.6],
                    [77.5, 8.9, 9.7, 10.7],
                    [78, 9.0, 9.8, 10.8],
                    [78.5, 9.1, 9.9, 10.9],
                    [79, 9.2, 10.0, 11.0],
                    [79.5, 9.3, 10.1, 11.1],
                    [80, 9.4, 10.2, 11.2],
                    [80.5, 9.5, 10.3, 11.3],
                    [81, 9.6, 10.4, 11.4],
                    [81.5, 9.7, 10.6, 11.6],
                    [82, 9.8, 10.7, 11.7],
                    [82.5, 9.9, 10.8, 11.8],
                    [83, 10.0, 10.9, 11.9],
                    [83.5, 10.1, 11.0, 12.1],
                    [84, 10.2, 11.1, 12.2],
                    [84.5, 10.3, 11.3, 12.3],
                    [85, 10.4, 11.4, 12.5],
                    [85.5, 10.6, 11.5, 12.6],
                    [86, 10.7, 11.6, 12.7],
                    [86.5, 10.8, 11.8, 12.9],
                    [87, 10.9, 11.9, 13.0],
                    [87.5, 11.0, 12.0, 13.2],
                    [88, 11.1, 12.1, 13.3],
                    [88.5, 11.2, 12.3, 13.4],
                    [89, 11.4, 12.4, 13.6],
                    [89.5, 11.5, 12.5, 13.7],
                    [90, 11.6, 12.6, 13.8],
                    [90.5, 11.7, 12.8, 14.0],
                    [91, 11.8, 12.9, 14.1],
                    [91.5, 11.9, 13.0, 14.3],
                    [92, 12.0, 13.1, 14.4],
                    [92.5, 12.1, 13.3, 14.5],
                    [93, 12.3, 13.4, 14.7],
                    [93.5, 12.4, 13.5, 14.8],
                    [94, 12.5, 13.6, 14.9],
                    [94.5, 12.6, 13.8, 15.1],
                    [95, 12.7, 13.9, 15.2],
                    [95.5, 12.8, 14.0, 15.4],
                    [96, 12.9, 14.1, 15.5],
                    [96.5, 13.1, 14.3, 15.6],
                    [97, 13.2, 14.4, 15.8],
                    [97.5, 13.3, 14.5, 15.9],
                    [98, 13.4, 14.7, 16.1],
                    [98.5, 13.5, 14.8, 16.2],
                    [99, 13.7, 14.9, 16.4],
                    [99.5, 13.8, 15.1, 16.5],
                    [100, 13.9, 15.2, 16.7],
                    [100.5, 14.1, 15.4, 16.9],
                    [101, 14.2, 15.5, 17.0],
                    [101.5, 14.3, 15.7, 17.2],
                    [102, 14.5, 15.8, 17.4],
                    [102.5, 14.6, 16.0, 17.5],
                    [103, 14.7, 16.1, 17.7],
                    [103.5, 14.9, 16.3, 17.9],
                    [104, 15.0, 16.4, 18.1],
                    [104.5, 15.2, 16.6, 18.2],
                    [105, 15.3, 16.8, 18.4],
                    [105.5, 15.5, 16.9, 18.6],
                    [106, 15.6, 17.1, 18.8],
                    [106.5, 15.8, 17.3, 19.0],
                    [107, 15.9, 17.5, 19.2],
                    [107.5, 16.1, 17.7, 19.4],
                    [108, 16.3, 17.8, 19.6],
                    [108.5, 16.4, 18.0, 19.8],
                    [109, 16.6, 18.2, 20.0],
                    [109.5, 16.8, 18.4, 20.3],
                    [110, 17.0, 18.6, 20.5],
                    [110.5, 17.1, 18.8, 20.7],
                    [111, 17.3, 19.0, 20.9],
                    [111.5, 17.5, 19.2, 21.2],
                    [112, 17.7, 19.4, 21.4],
                    [112.5, 17.9, 19.6, 21.6],
                    [113, 18.0, 19.8, 21.8],
                    [113.5, 18.2, 20.0, 22.1],
                    [114, 18.4, 20.2, 22.3],
                    [114.5, 18.6, 20.5, 22.6],
                    [115, 18.8, 20.7, 22.8],
                    [115.5, 19.0, 20.9, 23.0],
                    [116, 19.2, 21.1, 23.3],
                    [116.5, 19.4, 21.3, 23.5],
                    [117, 19.6, 21.5, 23.8],
                    [117.5, 19.8, 21.7, 24.0],
                    [118, 19.9, 22.0, 24.2],
                    [118.5, 20.1, 22.2, 24.5],
                    [119, 20.3, 22.4, 24.7],
                    [119.5, 20.5, 22.6, 25.0],
                    [120, 20.7, 22.8, 25.2],
                ];
            }

            $tolerance = 0.5;

            $closestRow = null;
            $closestDifference = PHP_FLOAT_MAX;

            foreach ($acuan as $row) {
                $difference = abs($row[0] - $bayi['panjang_badan']);
                if ($difference <= $tolerance && $difference < $closestDifference) {
                    $closestRow = $row;
                    $closestDifference = $difference;
                }
            }
            if ($closestRow) {
                list($A_closest, $B, $C, $D) = $closestRow;

                if ($bayi['berat_badan'] > $C) {
                    $median = $C;
                    $hitungatas = $bayi['berat_badan'] - $C;
                    $hitungbawah = $D - $C;
                    $bbu = $hitungatas / $hitungbawah;
                } else {
                    $median = $C;
                    $hitungatas = $bayi['berat_badan'] - $C;
                    $hitungbawah = $C - $B;
                    $bbu = $hitungatas / $hitungbawah;
                }
            }


            if ($bbu !== null) {
                if ($bbu < -3) {
                    $jenis = 'Gizi Buruk';
                } elseif ($bbu >= -3 && $bbu < -2) {
                    $jenis = 'Gizi Kurang';
                } elseif ($bbu >= -2 && $bbu <= 1) {
                    $jenis = 'Gizi Baik';
                } elseif ($bbu > 1 && $bbu <= 2) {
                    $jenis = 'Beresiko Gizi Lebih';
                } elseif ($bbu > 2 && $bbu <= 3) {
                    $jenis = 'Gizi Lebih';
                } elseif ($bbu > 3) {
                    $jenis = 'Obesitas';
                }
            }



            return [
                'jenis_kelamin' => $bayi['jenis_kelamin'],
                'hitungatas' => $hitungatas,
                'hitungbawah' => $hitungbawah,
                'id' => $bayi['id'],
                'nama' => $bayi['nama'],
                'umur_dalam_bulan' => $bayi['umur_dalam_bulan'],
                'berat_badan' => $bayi['berat_badan'],
                'panjang_badan' => $bayi['panjang_badan'],
                'bbu' => $bbu,
                'jenis' => $jenis,
                'median' => $median

            ];
        });



        return response()->json(array_merge($hasilLaki->toArray(), $hasilPerempuan->toArray()));
    }

    public function getberatbadantinggiPos($id)
    {
        $bayis = Bayi::with(['PeriksaBalita' => function ($query) {
            $query->join('kegiatan_posyandus', 'periksa_balitas.kegiatanposyandu_balita_id', '=', 'kegiatan_posyandus.id')
                ->whereHas('Kegiatanposyandu', function ($subQuery) {
                    $subQuery->where('status_kegiatan', 'selesai');
                })
                ->orderByRaw('STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y") DESC');
        }])->where('posyandus_id', $id)->get();

        $result = $bayis->map(function ($bayi) {
            $latestPeriksa = $bayi->PeriksaBalita->first();
            $tglKegiatan = $latestPeriksa ? $latestPeriksa->tgl_kegiatan : null;

            $umurDalamBulan = $tglKegiatan ? $this->getUmurDalamBulan($bayi->tanggal_lahir, $tglKegiatan) : null;

            return [
                'id' => $bayi->id,
                'nama' => $bayi->namabalita,
                'umur_dalam_bulan' => $umurDalamBulan,
                'jenis_kelamin' => $bayi->jenis_kelamin,
                'berat_badan' => $latestPeriksa ? $latestPeriksa->berat_badan : null,
                'panjang_badan' => $latestPeriksa ? $latestPeriksa->panjang_badan : null,
            ];
        });

        $bayiByGender = $result->groupBy('jenis_kelamin');

        $bayiLakiLaki = $bayiByGender->get('Laki-laki', collect());
        $bayiPerempuan = $bayiByGender->get('Perempuan', collect());

        $hasilLaki = $bayiLakiLaki->map(function ($bayi) {

            $bbu = 0;
            $median = null;
            $hitungatas = null;
            $hitungbawah = null;
            $bbu = null;
            $jenis = 'Tidak Diketahui';
            if ($bayi['umur_dalam_bulan'] >= 0 && $bayi['umur_dalam_bulan'] < 24) {
                $umurBulan = $bayi['umur_dalam_bulan'];
                $acuan = [
                    [45, 2.2, 2.4, 2.7],
                    [45.5, 2.3, 2.5, 2.8],
                    [46, 2.4, 2.6, 2.9],
                    [46.5, 2.5, 2.7, 3],
                    [47, 2.5, 2.8, 3],
                    [47.5, 2.6, 2.9, 3.1],
                    [48, 2.7, 2.9, 3.2],
                    [48.5, 2.8, 3, 3.3],
                    [49, 2.9, 3.1, 3.4],
                    [49.5, 3, 3.2, 3.5],
                    [50, 3, 3.3, 3.6],
                    [50.5, 3.1, 3.4, 3.8],
                    [51, 3.2, 3.5, 3.9],
                    [51.5, 3.3, 3.6, 4],
                    [52, 3.5, 3.8, 4.1],
                    [52.5, 3.6, 3.9, 4.2],
                    [53, 3.7, 4, 4.4],
                    [53.5, 3.8, 4.1, 4.5],
                    [54, 3.9, 4.3, 4.7],
                    [54.5, 4, 4.4, 4.8],
                    [55, 4.2, 4.5, 5],
                    [55.5, 4.3, 4.7, 5.1],
                    [56, 4.4, 4.8, 5.3],
                    [56.5, 4.6, 5, 5.4],
                    [57, 4.7, 5.1, 5.6],
                    [57.5, 4.9, 5.3, 5.7],
                    [58, 5, 5.4, 5.9],
                    [58.5, 5.1, 5.6, 6.1],
                    [59, 5.3, 5.7, 6.2],
                    [59.5, 5.4, 5.9, 6.4],
                    [60, 5.5, 6, 6.5],
                    [60.5, 5.6, 6.1, 6.7],
                    [61, 5.8, 6.3, 6.8],
                    [61.5, 5.9, 6.4, 7],
                    [62, 6, 6.5, 7.1],
                    [62.5, 6.1, 6.7, 7.2],
                    [63, 6.2, 6.8, 7.4],
                    [63.5, 6.4, 6.9, 7.5],
                    [64, 6.5, 7, 7.6],
                    [64.5, 6.6, 7.1, 7.8],
                    [65, 6.7, 7.3, 7.9],
                    [65.5, 6.8, 7.4, 8],
                    [66, 6.9, 7.5, 8.2],
                    [66.5, 7, 7.6, 8.3],
                    [67, 7.1, 7.7, 8.4],
                    [67.5, 7.2, 7.9, 8.5],
                    [68, 7.3, 8, 8.7],
                    [68.5, 7.5, 8.1, 8.8],
                    [69, 7.6, 8.2, 8.9],
                    [69.5, 7.7, 8.3, 9],
                    [70, 7.8, 8.4, 9.2],
                    [70.5, 7.9, 8.5, 9.3],
                    [71, 8, 8.6, 9.4],
                    [71.5, 8.1, 8.8, 9.5],
                    [72, 8.2, 8.9, 9.6],
                    [72.5, 8.3, 9, 9.8],
                    [73, 8.4, 9.1, 9.9],
                    [73.5, 8.5, 9.2, 10],
                    [74, 8.6, 9.3, 10.1],
                    [74.5, 8.7, 9.4, 10.2],
                    [75, 8.8, 9.5, 10.3],
                    [75.5, 8.8, 9.6, 10.4],
                    [76, 8.9, 9.7, 10.6],
                    [76.5, 9, 9.8, 10.7],
                    [77, 9.1, 9.9, 10.8],
                    [77.5, 9.2, 10, 10.9],
                    [78, 9.3, 10.1, 11],
                    [78.5, 9.4, 10.2, 11.1],
                    [79, 9.5, 10.3, 11.2],
                    [79.5, 9.5, 10.4, 11.3],
                    [80, 9.6, 10.4, 11.4],
                    [80.5, 9.7, 10.5, 11.5],
                    [81, 9.8, 10.6, 11.6],
                    [81.5, 9.9, 10.7, 11.7],
                    [82, 10, 10.8, 11.8],
                    [82.5, 10.1, 10.9, 11.9],
                    [83, 10.2, 11, 12],
                    [83.5, 10.3, 11.2, 12.1],
                    [84, 10.4, 11.3, 12.2],
                    [84.5, 10.5, 11.4, 12.4],
                    [85, 10.6, 11.5, 12.5],
                    [85.5, 10.7, 11.6, 12.6],
                    [86, 10.8, 11.7, 12.8],
                    [86.5, 11, 11.9, 12.9],
                    [87, 11.1, 12, 13],
                    [87.5, 11.2, 12.1, 13.2],
                    [88, 11.3, 12.2, 13.3],
                    [88.5, 11.4, 12.4, 13.4],
                    [89, 11.5, 12.5, 13.5],
                    [89.5, 11.6, 12.6, 13.7],
                    [90, 11.8, 12.7, 13.8],
                    [90.5, 11.9, 12.8, 13.9],
                    [91, 12, 13, 14.1],
                    [91.5, 12.1, 13.1, 14.2],
                    [92, 12.2, 13.2, 14.3],
                    [92.5, 12.3, 13.3, 14.4],
                    [93, 12.4, 13.4, 14.6],
                    [93.5, 12.5, 13.5, 14.7],
                    [94, 12.6, 13.7, 14.8],
                    [94.5, 12.7, 13.8, 14.9],
                    [95, 12.8, 13.9, 15.1],
                    [95.5, 12.9, 14, 15.2],
                    [96, 13.1, 14.1, 15.3],
                    [96.5, 13.2, 14.3, 15.5],
                    [97, 13.3, 14.4, 15.6],
                    [97.5, 13.4, 14.5, 15.7],
                    [98, 13.5, 14.6, 15.9],
                    [98.5, 13.6, 14.8, 16],
                    [99, 13.7, 14.9, 16.2],
                    [99.5, 13.9, 15, 16.3],
                    [100, 14, 15.2, 16.5],
                    [100.5, 14.1, 15.3, 16.6],
                    [101, 14.2, 15.4, 16.8],
                    [101.5, 14.4, 15.6, 16.9],
                    [102, 14.5, 15.7, 17.1],
                    [102.5, 14.6, 15.9, 17.3],
                    [103, 14.8, 16, 17.4],
                    [103.5, 14.9, 16.2, 17.6],
                    [104, 15, 16.3, 17.8],
                    [104.5, 15.2, 16.5, 17.9],
                    [105, 15.3, 16.6, 18.1],
                    [105.5, 15.4, 16.8, 18.3],
                    [106, 15.6, 16.9, 18.5],
                    [106.5, 15.7, 17.1, 18.6],
                    [107, 15.9, 17.3, 18.8],
                    [107.5, 16, 17.4, 19],
                    [108, 16.2, 17.6, 19.2],
                    [108.5, 16.3, 17.8, 19.4],
                    [109, 16.5, 17.9, 19.6],
                    [109.5, 16.6, 18.1, 19.8],
                    [110, 16.8, 18.3, 20],
                ];
            } elseif ($bayi['umur_dalam_bulan'] >= 24 && $bayi['umur_dalam_bulan'] <= 60) {
                $umurBulan = $bayi['umur_dalam_bulan'];
                $acuan = [
                    [65, 6.9, 7.4, 8.1],
                    [65.5, 7, 7.6, 8.2],
                    [66, 7.1, 7.7, 8.3],
                    [66.5, 7.2, 7.8, 8.5],
                    [67, 7.3, 7.9, 8.6],
                    [67.5, 7.4, 8, 8.7],
                    [68, 7.5, 8.1, 8.8],
                    [68.5, 7.6, 8.2, 9],
                    [69, 7.7, 8.4, 9.1],
                    [69.5, 7.8, 8.5, 9.2],
                    [70, 7.9, 8.6, 9.3],
                    [70.5, 8, 8.7, 9.5],
                    [71, 8.1, 8.8, 9.6],
                    [71.5, 8.2, 8.9, 9.7],
                    [72, 8.3, 9, 9.8],
                    [72.5, 8.4, 9.1, 9.9],
                    [73, 8.5, 9.2, 10],
                    [73.5, 8.6, 9.3, 10.2],
                    [74, 8.7, 9.4, 10.3],
                    [74.5, 8.8, 9.5, 10.4],
                    [75, 8.9, 9.6, 10.5],
                    [75.5, 9, 9.7, 10.6],
                    [76, 9.1, 9.8, 10.7],
                    [76.5, 9.2, 9.9, 10.8],
                    [77, 9.2, 10, 10.9],
                    [77.5, 9.3, 10.1, 11],
                    [78, 9.4, 10.2, 11.1],
                    [78.5, 9.5, 10.3, 11.2],
                    [79, 9.6, 10.4, 11.3],
                    [79.5, 9.7, 10.5, 11.4],
                    [80, 9.7, 10.6, 11.5],
                    [80.5, 9.8, 10.7, 11.6],
                    [81, 9.9, 10.8, 11.7],
                    [81.5, 10, 10.9, 11.8],
                    [82, 10.1, 11, 11.9],
                    [82.5, 10.2, 11.1, 12.1],
                    [83, 10.3, 11.2, 12.2],
                    [83.5, 10.4, 11.3, 12.3],
                    [84, 10.5, 11.4, 12.4],
                    [84.5, 10.7, 11.5, 12.5],
                    [85, 10.8, 11.7, 12.7],
                    [85.5, 10.9, 11.8, 12.8],
                    [86, 11, 11.9, 12.9],
                    [86.5, 11.1, 12, 13.1],
                    [87, 11.2, 12.2, 13.2],
                    [87.5, 11.3, 12.3, 13.3],
                    [88, 11.5, 12.4, 13.5],
                    [88.5, 11.6, 12.5, 13.6],
                    [89, 11.7, 12.6, 13.7],
                    [89.5, 11.8, 12.8, 13.9],
                    [90, 11.9, 12.9, 14],
                    [90.5, 12, 13, 14.1],
                    [91, 12.1, 13.1, 14.2],
                    [91.5, 12.2, 13.2, 14.4],
                    [92, 12.3, 13.4, 14.5],
                    [92.5, 12.4, 13.5, 14.6],
                    [93, 12.6, 13.6, 14.7],
                    [93.5, 12.7, 13.7, 14.9],
                    [94, 12.8, 13.8, 15],
                    [94.5, 12.9, 13.9, 15.1],
                    [95, 13, 14.1, 15.3],
                    [95.5, 13.1, 14.2, 15.4],
                    [96, 13.2, 14.3, 15.5],
                    [96.5, 13.3, 14.4, 15.7],
                    [97, 13.4, 14.6, 15.8],
                    [97.5, 13.6, 14.7, 15.9],
                    [98, 13.7, 14.8, 16.1],
                    [98.5, 13.8, 14.9, 16.2],
                    [99, 13.9, 15.1, 16.4],
                    [99.5, 14, 15.2, 16.5],
                    [100, 14.2, 15.4, 16.7],
                    [100.5, 14.3, 15.5, 16.9],
                    [101, 14.4, 15.6, 17],
                    [101.5, 14.5, 15.8, 17.2],
                    [102, 14.7, 15.9, 17.3],
                    [102.5, 14.8, 16.1, 17.5],
                    [103, 14.9, 16.2, 17.7],
                    [103.5, 15.1, 16.4, 17.8],
                    [104, 15.2, 16.5, 18],
                    [104.5, 15.4, 16.7, 18.2],
                    [105, 15.5, 16.8, 18.4],
                    [105.5, 15.6, 17, 18.5],
                    [106, 15.8, 17.2, 18.7],
                    [106.5, 15.9, 17.3, 18.9],
                    [107, 16.1, 17.5, 19.1],
                    [107.5, 16.2, 17.7, 19.3],
                    [108, 16.4, 17.8, 19.5],
                    [108.5, 16.5, 18, 19.7],
                    [109, 16.7, 18.2, 19.8],
                    [109.5, 16.8, 18.3, 20],
                    [110, 17, 18.5, 20.2],
                    [110.5, 17.1, 18.7, 20.4],
                    [111, 17.3, 18.9, 20.7],
                    [111.5, 17.5, 19.1, 20.9],
                    [112, 17.6, 19.2, 21.1],
                    [112.5, 17.8, 19.4, 21.3],
                    [113, 18, 19.6, 21.5],
                    [113.5, 18.1, 19.8, 21.7],
                    [114, 18.3, 20, 21.9],
                    [114.5, 18.5, 20.2, 22.1],
                    [115, 18.6, 20.4, 22.4],
                    [115.5, 18.8, 20.6, 22.6],
                    [116, 19, 20.8, 22.8],
                    [116.5, 19.2, 21, 23],
                    [117, 19.3, 21.2, 23.3],
                    [117.5, 19.5, 21.4, 23.5],
                    [118, 19.7, 21.6, 23.7],
                    [118.5, 19.9, 21.8, 23.9],
                    [119, 20, 22, 24.1],
                    [119.5, 20.2, 22.2, 24.4],
                    [120, 20.4, 22.4, 24.6],
                ];
            }

            $tolerance = 0.3;

            $closestRow = null;
            $closestDifference = PHP_FLOAT_MAX;

            foreach ($acuan as $row) {
                $difference = abs($row[0] - $bayi['panjang_badan']);
                if ($difference <= $tolerance && $difference < $closestDifference) {
                    $closestRow = $row;
                    $closestDifference = $difference;
                }
            }
            if ($closestRow) {
                list($A_closest, $B, $C, $D) = $closestRow;

                if ($bayi['berat_badan'] > $C) {
                    $median = $C;
                    $hitungatas = $bayi['berat_badan'] - $C;
                    $hitungbawah = $D - $C;
                    $bbu = $hitungatas / $hitungbawah;
                } else {
                    $median = $C;
                    $hitungatas = $bayi['berat_badan'] - $C;
                    $hitungbawah = $C - $B;
                    $bbu = $hitungatas / $hitungbawah;
                }
            }


            if ($bbu !== null) {
                if ($bbu < -3) {
                    $jenis = 'Gizi Buruk';
                } elseif ($bbu >= -3 && $bbu < -2) {
                    $jenis = 'Gizi Kurang';
                } elseif ($bbu >= -2 && $bbu <= 1) {
                    $jenis = 'Gizi Baik';
                } elseif ($bbu > 1 && $bbu <= 2) {
                    $jenis = 'Beresiko Gizi Lebih';
                } elseif ($bbu > 2 && $bbu <= 3) {
                    $jenis = 'Gizi Lebih';
                } elseif ($bbu > 3) {
                    $jenis = 'Obesitas';
                }
            }

            return [
                'jenis_kelamin' => $bayi['jenis_kelamin'],
                'hitungatas' => $hitungatas,
                'hitungbawah' => $hitungbawah,
                'id' => $bayi['id'],
                'nama' => $bayi['nama'],
                'umur_dalam_bulan' => $bayi['umur_dalam_bulan'],
                'berat_badan' => $bayi['berat_badan'],
                'panjang_badan' => $bayi['panjang_badan'],
                'bbu' => $bbu,
                'jenis' => $jenis,
                'median' => $median

            ];
        });


        $hasilPerempuan = $bayiPerempuan->map(function ($bayi) {
            $bbu = 0;
            $median = null;
            $hitungatas = null;
            $hitungbawah = null;
            $bbu = null;
            $jenis = 'Tidak Diketahui';

            if ($bayi['umur_dalam_bulan'] >= 0 && $bayi['umur_dalam_bulan'] < 24) {
                $umurBulan = $bayi['umur_dalam_bulan'];
                $acuan = [
                    [45, 2.3, 2.5, 2.7],
                    [45.5, 2.3, 2.5, 2.8],
                    [46, 2.4, 2.6, 2.9],
                    [46.5, 2.5, 2.7, 3],
                    [47, 2.6, 2.8, 3.1],
                    [47.5, 2.6, 2.9, 3.2],
                    [48, 2.7, 3, 3.3],
                    [48.5, 2.8, 3.1, 3.4],
                    [49, 2.9, 3.2, 3.5],
                    [49.5, 3, 3.3, 3.6],
                    [50, 3.1, 3.4, 3.7],
                    [50.5, 3.2, 3.5, 3.8],
                    [51, 3.3, 3.6, 3.9],
                    [51.5, 3.4, 3.7, 4],
                    [52, 3.5, 3.8, 4.2],
                    [52.5, 3.6, 3.9, 4.3],
                    [53, 3.7, 4, 4.4],
                    [53.5, 3.8, 4.2, 4.6],
                    [54, 3.9, 4.3, 4.7],
                    [54.5, 4, 4.4, 4.8],
                    [55, 4.2, 4.5, 5],
                    [55.5, 4.3, 4.7, 5.1],
                    [56, 4.4, 4.8, 5.3],
                    [56.5, 4.5, 5, 5.4],
                    [57, 4.6, 5.1, 5.6],
                    [57.5, 4.8, 5.2, 5.7],
                    [58, 4.9, 5.4, 5.9],
                    [58.5, 5, 5.5, 6],
                    [59, 5.1, 5.6, 6.2],
                    [59.5, 5.3, 5.7, 6.3],
                    [60, 5.4, 5.9, 6.4],
                    [60.5, 5.5, 6, 6.6],
                    [61, 5.6, 6.1, 6.7],
                    [61.5, 5.7, 6.3, 6.9],
                    [62, 5.8, 6.4, 7],
                    [62.5, 5.9, 6.5, 7.1],
                    [63, 6, 6.6, 7.3],
                    [63.5, 6.2, 6.7, 7.4],
                    [64, 6.3, 6.9, 7.5],
                    [64.5, 6.4, 7, 7.6],
                    [65, 6.5, 7.1, 7.8],
                    [65.5, 6.6, 7.2, 7.9],
                    [66, 6.7, 7.3, 8],
                    [66.5, 6.8, 7.4, 8.1],
                    [67, 6.9, 7.5, 8.3],
                    [67.5, 7, 7.6, 8.4],
                    [68, 7.1, 7.7, 8.5],
                    [68.5, 7.2, 7.9, 8.6],
                    [69, 7.3, 8, 8.7],
                    [69.5, 7.4, 8.1, 8.8],
                    [70, 7.5, 8.2, 9],
                    [70.5, 7.6, 8.3, 9.1],
                    [71, 7.7, 8.4, 9.2],
                    [71.5, 7.7, 8.5, 9.3],
                    [72, 7.8, 8.6, 9.4],
                    [72.5, 7.9, 8.7, 9.5],
                    [73, 8, 8.8, 9.6],
                    [73.5, 8.1, 8.9, 9.7],
                    [74, 8.2, 9, 9.8],
                    [74.5, 8.3, 9.1, 9.9],
                    [75, 8.4, 9.1, 10],
                    [75.5, 8.5, 9.2, 10.1],
                    [76, 8.5, 9.3, 10.2],
                    [76.5, 8.6, 9.4, 10.3],
                    [77, 8.7, 9.5, 10.4],
                    [77.5, 8.8, 9.6, 10.5],
                    [78, 8.9, 9.7, 10.6],
                    [78.5, 9, 9.8, 10.7],
                    [79, 9.1, 9.9, 10.8],
                    [79.5, 9.1, 10, 10.9],
                    [80, 9.2, 10.1, 11],
                    [80.5, 9.3, 10.2, 11.2],
                    [81, 9.4, 10.3, 11.3],
                    [81.5, 9.5, 10.4, 11.4],
                    [82, 9.6, 10.5, 11.5],
                    [82.5, 9.7, 10.6, 11.6],
                    [83, 9.8, 10.7, 11.8],
                    [83.5, 9.9, 10.9, 11.9],
                    [84, 10.1, 11, 12],
                    [84.5, 10.2, 11.1, 12.1],
                    [85, 10.3, 11.2, 12.3],
                    [85.5, 10.4, 11.3, 12.4],
                    [86, 10.5, 11.5, 12.6],
                    [86.5, 10.6, 11.6, 12.7],
                    [87, 10.7, 11.7, 12.8],
                    [87.5, 10.9, 11.8, 13],
                    [88, 11, 12, 13.1],
                    [88.5, 11.1, 12.1, 13.2],
                    [89, 11.2, 12.2, 13.4],
                    [89.5, 11.3, 12.3, 13.5],
                    [90, 11.4, 12.5, 13.7],
                    [90.5, 11.5, 12.6, 13.8],
                    [91, 11.7, 12.7, 13.9],
                    [91.5, 11.8, 12.8, 14.1],
                    [92, 11.9, 13, 14.2],
                    [92.5, 12, 13.1, 14.3],
                    [93, 12.1, 13.2, 14.5],
                    [93.5, 12.2, 13.3, 14.6],
                    [94, 12.3, 13.5, 14.7],
                    [94.5, 12.4, 13.6, 14.9],
                    [95, 12.6, 13.7, 15],
                    [95.5, 12.7, 13.8, 15.2],
                    [96, 12.8, 14, 15.3],
                    [96.5, 12.9, 14.1, 15.4],
                    [97, 13, 14.2, 15.6],
                    [97.5, 13.1, 14.4, 15.7],
                    [98, 13.3, 14.5, 15.9],
                    [98.5, 13.4, 14.6, 16],
                    [99, 13.5, 14.8, 16.2],
                    [99.5, 13.6, 14.9, 16.3],
                    [100, 13.7, 15, 16.5],
                    [100.5, 13.9, 15.2, 16.6],
                    [101, 14, 15.3, 16.8],
                    [101.5, 14.1, 15.5, 17],
                    [102, 14.3, 15.6, 17.1],
                    [102.5, 14.4, 15.8, 17.3],
                    [103, 14.5, 15.9, 17.5],
                    [103.5, 14.7, 16.1, 17.6],
                    [104, 14.8, 16.2, 17.8],
                    [104.5, 15, 16.4, 18],
                    [105, 15.1, 16.5, 18.2],
                    [105.5, 15.3, 16.7, 18.4],
                    [106, 15.4, 16.9, 18.5],
                    [106.5, 15.6, 17.1, 18.7],
                    [107, 15.7, 17.2, 18.9],
                    [107.5, 15.9, 17.4, 19.1],
                    [108, 16, 17.6, 19.3],
                    [108.5, 16.2, 17.8, 19.5],
                    [109, 16.4, 18, 19.7],
                    [109.5, 16.5, 18.1, 20],
                    [110, 16.7, 18.3, 20.2],
                ];
            } elseif ($bayi['umur_dalam_bulan'] >= 24 && $bayi['umur_dalam_bulan'] <= 60) {
                $umurBulan = $bayi['umur_dalam_bulan'];
                $acuan = [
                    [65, 6.6, 7.2, 7.9],
                    [65.5, 6.7, 7.4, 8.1],
                    [66, 6.8, 7.5, 8.2],
                    [66.5, 6.9, 7.6, 8.3],
                    [67, 7.0, 7.7, 8.4],
                    [67.5, 7.1, 7.8, 8.5],
                    [68, 7.2, 7.9, 8.7],
                    [68.5, 7.3, 8.0, 8.8],
                    [69, 7.4, 8.1, 8.9],
                    [69.5, 7.5, 8.2, 9.0],
                    [70, 7.6, 8.3, 9.1],
                    [70.5, 7.7, 8.4, 9.2],
                    [71, 7.8, 8.5, 9.3],
                    [71.5, 7.9, 8.6, 9.4],
                    [72, 8.0, 8.7, 9.5],
                    [72.5, 8.1, 8.8, 9.7],
                    [73, 8.1, 8.9, 9.8],
                    [73.5, 8.2, 9.0, 9.9],
                    [74, 8.3, 9.1, 10.0],
                    [74.5, 8.4, 9.2, 10.1],
                    [75, 8.5, 9.3, 10.2],
                    [75.5, 8.6, 9.4, 10.3],
                    [76, 8.7, 9.5, 10.4],
                    [76.5, 8.7, 9.6, 10.5],
                    [77, 8.8, 9.6, 10.6],
                    [77.5, 8.9, 9.7, 10.7],
                    [78, 9.0, 9.8, 10.8],
                    [78.5, 9.1, 9.9, 10.9],
                    [79, 9.2, 10.0, 11.0],
                    [79.5, 9.3, 10.1, 11.1],
                    [80, 9.4, 10.2, 11.2],
                    [80.5, 9.5, 10.3, 11.3],
                    [81, 9.6, 10.4, 11.4],
                    [81.5, 9.7, 10.6, 11.6],
                    [82, 9.8, 10.7, 11.7],
                    [82.5, 9.9, 10.8, 11.8],
                    [83, 10.0, 10.9, 11.9],
                    [83.5, 10.1, 11.0, 12.1],
                    [84, 10.2, 11.1, 12.2],
                    [84.5, 10.3, 11.3, 12.3],
                    [85, 10.4, 11.4, 12.5],
                    [85.5, 10.6, 11.5, 12.6],
                    [86, 10.7, 11.6, 12.7],
                    [86.5, 10.8, 11.8, 12.9],
                    [87, 10.9, 11.9, 13.0],
                    [87.5, 11.0, 12.0, 13.2],
                    [88, 11.1, 12.1, 13.3],
                    [88.5, 11.2, 12.3, 13.4],
                    [89, 11.4, 12.4, 13.6],
                    [89.5, 11.5, 12.5, 13.7],
                    [90, 11.6, 12.6, 13.8],
                    [90.5, 11.7, 12.8, 14.0],
                    [91, 11.8, 12.9, 14.1],
                    [91.5, 11.9, 13.0, 14.3],
                    [92, 12.0, 13.1, 14.4],
                    [92.5, 12.1, 13.3, 14.5],
                    [93, 12.3, 13.4, 14.7],
                    [93.5, 12.4, 13.5, 14.8],
                    [94, 12.5, 13.6, 14.9],
                    [94.5, 12.6, 13.8, 15.1],
                    [95, 12.7, 13.9, 15.2],
                    [95.5, 12.8, 14.0, 15.4],
                    [96, 12.9, 14.1, 15.5],
                    [96.5, 13.1, 14.3, 15.6],
                    [97, 13.2, 14.4, 15.8],
                    [97.5, 13.3, 14.5, 15.9],
                    [98, 13.4, 14.7, 16.1],
                    [98.5, 13.5, 14.8, 16.2],
                    [99, 13.7, 14.9, 16.4],
                    [99.5, 13.8, 15.1, 16.5],
                    [100, 13.9, 15.2, 16.7],
                    [100.5, 14.1, 15.4, 16.9],
                    [101, 14.2, 15.5, 17.0],
                    [101.5, 14.3, 15.7, 17.2],
                    [102, 14.5, 15.8, 17.4],
                    [102.5, 14.6, 16.0, 17.5],
                    [103, 14.7, 16.1, 17.7],
                    [103.5, 14.9, 16.3, 17.9],
                    [104, 15.0, 16.4, 18.1],
                    [104.5, 15.2, 16.6, 18.2],
                    [105, 15.3, 16.8, 18.4],
                    [105.5, 15.5, 16.9, 18.6],
                    [106, 15.6, 17.1, 18.8],
                    [106.5, 15.8, 17.3, 19.0],
                    [107, 15.9, 17.5, 19.2],
                    [107.5, 16.1, 17.7, 19.4],
                    [108, 16.3, 17.8, 19.6],
                    [108.5, 16.4, 18.0, 19.8],
                    [109, 16.6, 18.2, 20.0],
                    [109.5, 16.8, 18.4, 20.3],
                    [110, 17.0, 18.6, 20.5],
                    [110.5, 17.1, 18.8, 20.7],
                    [111, 17.3, 19.0, 20.9],
                    [111.5, 17.5, 19.2, 21.2],
                    [112, 17.7, 19.4, 21.4],
                    [112.5, 17.9, 19.6, 21.6],
                    [113, 18.0, 19.8, 21.8],
                    [113.5, 18.2, 20.0, 22.1],
                    [114, 18.4, 20.2, 22.3],
                    [114.5, 18.6, 20.5, 22.6],
                    [115, 18.8, 20.7, 22.8],
                    [115.5, 19.0, 20.9, 23.0],
                    [116, 19.2, 21.1, 23.3],
                    [116.5, 19.4, 21.3, 23.5],
                    [117, 19.6, 21.5, 23.8],
                    [117.5, 19.8, 21.7, 24.0],
                    [118, 19.9, 22.0, 24.2],
                    [118.5, 20.1, 22.2, 24.5],
                    [119, 20.3, 22.4, 24.7],
                    [119.5, 20.5, 22.6, 25.0],
                    [120, 20.7, 22.8, 25.2],
                ];
            }

            $tolerance = 0.5;

            $closestRow = null;
            $closestDifference = PHP_FLOAT_MAX;

            foreach ($acuan as $row) {
                $difference = abs($row[0] - $bayi['panjang_badan']);
                if ($difference <= $tolerance && $difference < $closestDifference) {
                    $closestRow = $row;
                    $closestDifference = $difference;
                }
            }
            if ($closestRow) {
                list($A_closest, $B, $C, $D) = $closestRow;

                if ($bayi['berat_badan'] > $C) {
                    $median = $C;
                    $hitungatas = $bayi['berat_badan'] - $C;
                    $hitungbawah = $D - $C;
                    $bbu = $hitungatas / $hitungbawah;
                } else {
                    $median = $C;
                    $hitungatas = $bayi['berat_badan'] - $C;
                    $hitungbawah = $C - $B;
                    $bbu = $hitungatas / $hitungbawah;
                }
            }


            if ($bbu !== null) {
                if ($bbu < -3) {
                    $jenis = 'Gizi Buruk';
                } elseif ($bbu >= -3 && $bbu < -2) {
                    $jenis = 'Gizi Kurang';
                } elseif ($bbu >= -2 && $bbu <= 1) {
                    $jenis = 'Gizi Baik';
                } elseif ($bbu > 1 && $bbu <= 2) {
                    $jenis = 'Beresiko Gizi Lebih';
                } elseif ($bbu > 2 && $bbu <= 3) {
                    $jenis = 'Gizi Lebih';
                } elseif ($bbu > 3) {
                    $jenis = 'Obesitas';
                }
            }



            return [
                'jenis_kelamin' => $bayi['jenis_kelamin'],
                'hitungatas' => $hitungatas,
                'hitungbawah' => $hitungbawah,
                'id' => $bayi['id'],
                'nama' => $bayi['nama'],
                'umur_dalam_bulan' => $bayi['umur_dalam_bulan'],
                'berat_badan' => $bayi['berat_badan'],
                'panjang_badan' => $bayi['panjang_badan'],
                'bbu' => $bbu,
                'jenis' => $jenis,
                'median' => $median

            ];
        });



        return response()->json(array_merge($hasilLaki->toArray(), $hasilPerempuan->toArray()));
    }
}
