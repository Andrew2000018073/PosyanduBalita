<?php

namespace App\Http\Controllers;

use App\Models\Wus;
use App\Models\Kehamilan;
use App\Models\periksawus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PendaftaranIbuHamil;
use Illuminate\Support\Facades\Log;

class WusController extends Controller
{
    public function getOrtu()
    {
        $data = Wus::with('PUS:nama_suami')->get();
        return response()->json($data);
    }
    public function getWus()
    {
        $wusWithLatestLila = Wus::with(['periksawus' => function ($query) {
            $query->whereHas('Kegiatanposyandu', function ($subQuery) {
                $subQuery->where('status_kegiatan', 'selesai');
            })->latest('created_at')->take(1);
        }, 'posyandu'])->get();
        $latestLilaData = [];
        foreach ($wusWithLatestLila as $wus) {
            $latestPeriksa = $wus->periksawus->first();
            $lilaTerbaru = $latestPeriksa ? $latestPeriksa->lila_periksa : null;
            if (is_null($lilaTerbaru)) {
                $lilaTerbaru = $this->getLatestLilaFromPreviousActivities($wus->id);
            }
            $latestLilaData[] = [
                'wus_id' => $wus->id,
                'nama' => $wus->nama,
                'lila_periksa' => $lilaTerbaru,
                'lila_wus' => $wus->lila_wus,
                'tanggal_lahir' => $wus->tanggal_lahir,
                'statuswus' => $wus->statuswus,
                'posyandus_id' => $wus->posyandus_id,
                'posyandu_nama' => optional($wus->posyandu)->nama_posyandu,
            ];
        }
        return response()->json($latestLilaData);
    }

    public function getWusPosyandu($id)
    {
        $wusWithLatestLila = Wus::with(['periksawus' => function ($query) {
            $query->whereHas('Kegiatanposyandu', function ($subQuery) {
                $subQuery->where('status_kegiatan', 'selesai');
            })->latest('created_at')->take(1);
        }, 'posyandu'])->where('posyandus_id', $id)->get();

        $latestLilaData = [];

        foreach ($wusWithLatestLila as $wus) {
            $latestPeriksa = $wus->periksawus->first();

            $lilaTerbaru = $latestPeriksa ? $latestPeriksa->lila_periksa : null;

            if (is_null($lilaTerbaru)) {
                $lilaTerbaru = $this->getLatestLilaFromPreviousActivities($wus->id);
            }

            $latestLilaData[] = [
                'wus_id' => $wus->id,
                'nama' => $wus->nama,
                'lila_periksa' => $lilaTerbaru,
                'tanggal_lahir' => $wus->tanggal_lahir,
                'statuswus' => $wus->statuswus,
                'posyandu_id' => $wus->posyandus_id,
                'posyandu_nama' => optional($wus->posyandu)->nama_posyandu,
            ];
        }

        return response()->json($latestLilaData);
    }

    public function getWusPeserta($id)
    {
        return response()->json(Wus::get()->where('posyandus_id', $id));
    }

    public function getJumlahWus()
    {
        return response()->json(Wus::count());
    }
    public function getJumlahWusPosyandu($id)
    {
        return response()->json(Wus::where('posyandus_id', $id)->count());
    }

    public function getWusStatus()
    {
        $tdmenikah = Wus::where('statuswus', 'Tidak menikah')->count();
        $hamil = Wus::where('statuswus', 'Hamil')->count();
        $tdhamilsusu = Wus::where('statuswus', 'Tidak hamil & Menyusui')->count();
        $tdhamilanggur = Wus::where('statuswus', 'Tidak hamil & Tidak menyusui')->count();
        $nifas = Wus::where('statuswus', 'Nifas')->count();

        return response()->json(compact('tdmenikah', 'hamil', 'tdhamilsusu', 'tdhamilanggur', 'nifas'));
    }
    public function getWusStatusPos($id)
    {
        $tdmenikah = Wus::where('statuswus', 'Tidak menikah')->where('posyandus_id', $id)->count();
        $hamil = Wus::where('statuswus', 'Hamil')->where('posyandus_id', $id)->count();
        $tdhamilsusu = Wus::where('statuswus', 'Tidak hamil & Menyusui')->where('posyandus_id', $id)->count();
        $tdhamilanggur = Wus::where('statuswus', 'Tidak hamil & Tidak menyusui')->where('posyandus_id', $id)->count();
        $nifas = Wus::where('statuswus', 'Nifas')->where('posyandus_id', $id)->count();

        return response()->json(compact('tdmenikah', 'hamil', 'tdhamilsusu', 'tdhamilanggur', 'nifas'));
    }
    public function storeWus(Request $request)
    {
        Wus::create([
            'nama' => $request->nama,
            'lila_wus' => $request->lila_wus,
            'tanggal_lahir' => $request->tanggal_lahir,
            'posyandus_id' => $request->posyandus_id,
            'statuswus' => $request->statuswus,
        ]);
    }
    public function updateWus(Request $request)
    {
        $updated = Wus::where('id', $request->id)->update([
            'nama' => $request->nama,
            'lila_wus' => $request->lila_wus,
            'tanggal_lahir' => $request->tanggal_lahir,
            'statuswus' => $request->statuswus,
            'posyandus_id' => $request->posyandus_id,
        ]);
        return response()->json($updated ? 'Update successful' : 'Update failed');
    }
    public function updateWusPeserta(Request $request)
    {
        $updated = Wus::where('id', $request->wus_id)->update([
            'statuswus' => $request->statuswus,
            'lila_wus' => $request->lila_periksa,
        ]);
        return response()->json($updated ? 'Update successful' : 'Update failed');
    }
    public function storeWusA(Request $request)
    {
        Wus::create([
            'nama' => $request->nama,
            'lila_wus' => $request->lila_wus,
            'tanggal_lahir' => $request->tanggal_lahir,
            'posyandus_id' => $request->posyandus_id,
            'statuswus' => $request->statuswus,
        ]);
    }

    public function storeWusHamil(Request $request)
    {
        Wus::create([
            'nama' => $request->nama,
            'lila_wus' => $request->lila_wus,
            'tanggal_lahir' => $request->tanggal_lahir,
            'posyandus_id' => $request->posyandus_id,
            'statuswus' => $request->statuswus,
        ]);
    }

    public function deleteWus($id)
    {
        Wus::where('id', $id)->delete();
        return response()->json('Dihapus');
    }

    public function getGemuk()
    {
        $wusWithLatestlingper = Wus::with(['periksawus' => function ($query) {
            $query->whereHas('Kegiatanposyandu', function ($subQuery) {
                $subQuery->where('status_kegiatan', 'selesai')->latest('tgl_kegiatan');
            })->take(1);
        }])->get();

        $latestLiperData = [];

        foreach ($wusWithLatestlingper as $wus) {
            $latestPeriksa = $wus->periksawus->first();

            $lingkarperut = null;

            if ($latestPeriksa) {
                $lingkarperut = $latestPeriksa->lingkarperut_periksa;
            }

            if (is_null($lingkarperut)) {
                $lingkarperut = $this->getLatestLilaFromPreviousActivities($wus->id);
            }

            $latestLiperData[] = [
                'wus_id' => $wus->id,
                'nama' => $wus->nama,
                'lingkarperut_periksa' => $lingkarperut,
            ];
        }

        return response()->json($latestLiperData);
    }

    public function getGemukPos($id)
    {
        $wusWithLatestlingper = Wus::with(['periksawus' => function ($query) {
            $query->whereHas('Kegiatanposyandu', function ($subQuery) {
                $subQuery->where('status_kegiatan', 'selesai')->latest('tgl_kegiatan');
            })->take(1);
        }])->where('posyandus_id', $id)->get();

        $latestLilaData = [];

        foreach ($wusWithLatestlingper as $wus) {
            $latestPeriksa = $wus->periksawus->first();

            $lingkarperut = null;

            if ($latestPeriksa) {
                $lingkarperut = $latestPeriksa->lingkarperut_periksa;
            }
            if (is_null($lingkarperut)) {
                $lingkarperut = $this->getLatestLilaFromPreviousActivities($wus->id);
            }
            $latestLilaData[] = [
                'wus_id' => $wus->id,
                'nama' => $wus->nama,
                'lingkarperut_periksa' => $lingkarperut,
            ];
        }
        return response()->json($latestLilaData);
    }
    public function getGemukPosByDate($id, $date)
    {
        // Format $date menjadi bulan dan tahun

        $inputDate = \Carbon\Carbon::createFromFormat('d-m-Y', $date);
        $inputMonth = $inputDate->format('m');
        $inputYear = $inputDate->format('Y');
        // dd($inputMonth, $inputYear);


        $wusWithLatestlingper = Wus::with(['periksawus' => function ($query) use ($inputMonth, $inputYear) {
            $query->whereHas('Kegiatanposyandu', function ($subQuery) use ($inputMonth, $inputYear) {
                $subQuery->where('status_kegiatan', 'selesai')
                    ->whereMonth(DB::raw("STR_TO_DATE(tgl_kegiatan, '%d-%m-%Y')"), $inputMonth)
                    ->whereYear(DB::raw("STR_TO_DATE(tgl_kegiatan, '%d-%m-%Y')"), $inputYear);
            })->take(1);
        }])->where('posyandus_id', $id)->get();
        // dd($wusWithLatestlingper);

        $latestLilaData = [];

        foreach ($wusWithLatestlingper as $wus) {
            $latestPeriksa = $wus->periksawus->first();

            $lingkarperut = null;

            if ($latestPeriksa) {
                $lingkarperut = $latestPeriksa->lingkarperut_periksa;
            }
            if (is_null($lingkarperut)) {
                $lingkarperut = $this->getLatestLilaFromPreviousActivities($wus->id);
            }
            $latestLilaData[] = [
                'wus_id' => $wus->id,
                'nama' => $wus->nama,
                'lingkarperut_periksa' => $lingkarperut,
            ];
        }

        return response()->json($latestLilaData);
    }

    public function getKek()
    {
        $wusWithLatestLila = Wus::with(['periksawus' => function ($query) {
            $query->whereHas('Kegiatanposyandu', function ($subQuery) {
                $subQuery->where('status_kegiatan', 'selesai');
            })->latest('created_at')->take(1);
        }])->where('statuswus', 'Hamil')->get();

        $latestLilaData = [];

        foreach ($wusWithLatestLila as $wus) {
            $latestPeriksa = $wus->periksawus->first();

            $lilaTerbaru = $latestPeriksa ? $latestPeriksa->lila_periksa : null;

            if (is_null($lilaTerbaru)) {
                $lilaTerbaru = $this->getLatestLilaFromPreviousActivities($wus->id);
            }

            $latestLilaData[] = [
                'wus_id' => $wus->id,
                'nama' => $wus->nama,
                'lila_periksa' => $lilaTerbaru,
            ];
        }

        return response()->json($latestLilaData);
    }
    public function getKekPos($id)
    {
        $wusWithLatestLila = Wus::with(['periksawus' => function ($query) {
            $query->whereHas('Kegiatanposyandu', function ($subQuery) {
                $subQuery->where('status_kegiatan', 'selesai');
            })->latest('created_at')->take(1);
        }])
            ->where('posyandus_id', $id)->where('statuswus', 'Hamil')
            ->get();

        $latestLilaData = [];

        foreach ($wusWithLatestLila as $wus) {
            $latestPeriksa = $wus->periksawus->first();

            $lilaTerbaru = $latestPeriksa ? $latestPeriksa->lila_periksa : $this->getLatestLilaFromPreviousActivities($wus->id);

            if (!is_null($lilaTerbaru)) {
                $latestLilaData[] = [
                    'wus_id' => $wus->id,
                    'nama' => $wus->nama,
                    'lila_periksa' => $lilaTerbaru,
                ];
            }
        }

        return response()->json($latestLilaData);
    }




    private function getLatestLilaFromPreviousActivities($wusId)
    {
        $periksaWus = PeriksaWus::where('wus_id', $wusId)
            ->whereHas('Kegiatanposyandu', function ($query) {
                $query->where('status_kegiatan', 'selesai');
            })
            ->latest('created_at')
            ->first();

        return $periksaWus ? $periksaWus->lila_periksa : null;
    }

    public function getKekLast12Months()
    {
        $currentDate = now();
        $monthsNames = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        ];

        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $monthIndex = intval($date->format('m'));
            $months[$date->format('Y-m')] = [
                'KEK' => 0,
                'Normal' => 0,
                'month' => $monthsNames[$monthIndex]
            ];
        }

        $data = PeriksaWus::selectRaw('DATE_FORMAT(STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y"), "%Y-%m") as month,
        SUM(CASE WHEN lila_periksa < 23.5 THEN 1 ELSE 0 END) as KEK,
        SUM(CASE WHEN lila_periksa >= 23.5 THEN 1 ELSE 0 END) as Normal')
            ->join('kegiatan_posyandus', 'periksawuses.kegiatanposyandu_w_u_s_id', '=', 'kegiatan_posyandus.id')
            ->where('kegiatan_posyandus.status_kegiatan', 'selesai')
            ->where('periksawuses.statusperiksa', 'Hamil')
            ->whereRaw('STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y") >= ?', [$currentDate->subMonths(12)->format('Y-m-d')])
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        foreach ($data as $entry) {
            if (isset($months[$entry->month])) {
                $months[$entry->month]['KEK'] = $entry->KEK;
                $months[$entry->month]['Normal'] = $entry->Normal;
            }
        }

        foreach ($months as $month => &$values) {
            if (!isset($values['month'])) {
                $values['month'] = $monthsNames[(int)date('m', strtotime($month))];
            }
        }

        return response()->json(array_values($months));
    }



    public function getKekLast12MonthsPos($id)
    {
        $currentDate = now();
        $monthsNames = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        ];

        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $monthIndex = intval($date->format('m'));
            $months[$date->format('Y-m')] = [
                'KEK' => 0,
                'Normal' => 0,
                'month' => $monthsNames[$monthIndex]
            ];
        }

        $data = PeriksaWus::selectRaw('DATE_FORMAT(STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y"), "%Y-%m") as month,
            SUM(CASE WHEN lila_periksa < 23.5 THEN 1 ELSE 0 END) as KEK,
            SUM(CASE WHEN lila_periksa >= 23.5 THEN 1 ELSE 0 END) as Normal')
            ->join('kegiatan_posyandus', 'periksawuses.kegiatanposyandu_w_u_s_id', '=', 'kegiatan_posyandus.id')
            ->where('kegiatan_posyandus.status_kegiatan', 'selesai')
            ->where('kegiatan_posyandus.posyandu_id', $id)
            ->where('periksawuses.statusperiksa', 'Hamil')
            ->whereRaw('STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y") >= ?', [$currentDate->subMonths(12)->format('Y-m-d')])
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        foreach ($data as $entry) {
            if (isset($months[$entry->month])) {
                $months[$entry->month]['KEK'] = $entry->KEK;
                $months[$entry->month]['Normal'] = $entry->Normal;
            }
        }

        foreach ($months as $month => &$values) {
            if (!isset($values['month'])) {
                $values['month'] = $monthsNames[(int)date('m', strtotime($month))];
            }
        }

        return response()->json(array_values($months));
    }

    public function getDetail($id)
    {
        $posyandu = Wus::where('id', $id)->first();
        if ($posyandu) {
            return response()->json($posyandu);
        } else {
            return response()->json(['message' => 'Wus not found'], 404);
        }
    }

    public function getPeriksaLast12Monthsall()
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
                'lila_periksa' => null,
                'lingkarperut_periksa' => null,
                'diastol' => null,
                'sistol' => null,
                'tinggi_fundus' => null,
                'keluhan' => null,
                'statusperiksa' => null,
            ];
        }

        $data = PeriksaWus::selectRaw('
            DATE_FORMAT(STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y"), "%Y-%m") as month,
            AVG(lila_periksa) as lila_periksa,
            AVG(lingkarperut_periksa) as lingkarperut_periksa,
            AVG(diastol) as diastol,
            AVG(sistol) as sistol,
            AVG(tinggi_fundus) as tinggi_fundus,
            GROUP_CONCAT(keluhan SEPARATOR ", ") as keluhan,
            statusperiksa
        ')
            ->join('kegiatan_posyandus', 'periksawuses.kegiatanposyandu_w_u_s_id', '=', 'kegiatan_posyandus.id')
            ->whereRaw('STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y") >= ?', [$currentDate->subMonths(12)->format('Y-m-d')])
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        foreach ($data as $entry) {
            if (isset($months[$entry->month])) {
                $months[$entry->month]['lila_periksa'] = round($entry->lila_periksa, 2);
                $months[$entry->month]['lingkarperut_periksa'] = round($entry->lingkarperut_periksa, 2);
                $months[$entry->month]['diastol'] = round($entry->diastol, 2);
                $months[$entry->month]['sistol'] = round($entry->sistol, 2);
                $months[$entry->month]['tinggi_fundus'] = round($entry->tinggi_fundus, 2);
                $months[$entry->month]['keluhan'] = $entry->keluhan;
                $months[$entry->month]['statusperiksa'] = $entry->statusperiksa;
            }
        }


        return response()->json(array_values($months));
    }
    public function getPeriksaLast12Months($id)
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
                'lila_periksa' => null,
                'lingkarperut_periksa' => null,
                'diastol' => null,
                'sistol' => null,
                'tinggi_fundus' => null,
                'keluhan' => null,
                'statusperiksa' => null,
            ];
        }

        $data = PeriksaWus::selectRaw('
            DATE_FORMAT(STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y"), "%Y-%m") as month,
            AVG(lila_periksa) as lila_periksa,
            AVG(lingkarperut_periksa) as lingkarperut_periksa,
            AVG(diastol) as diastol,
            AVG(sistol) as sistol,
            AVG(tinggi_fundus) as tinggi_fundus,
            GROUP_CONCAT(keluhan SEPARATOR ", ") as keluhan,
            statusperiksa
        ')
            ->join('kegiatan_posyandus', 'periksawuses.kegiatanposyandu_w_u_s_id', '=', 'kegiatan_posyandus.id')
            ->where('wus_id', $id)
            ->whereRaw('STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y") >= ?', [$currentDate->subMonths(12)->format('Y-m-d')])
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        foreach ($data as $entry) {
            if (isset($months[$entry->month])) {
                $months[$entry->month]['lila_periksa'] = round($entry->lila_periksa, 2);
                $months[$entry->month]['lingkarperut_periksa'] = round($entry->lingkarperut_periksa, 2);
                $months[$entry->month]['diastol'] = round($entry->diastol, 2);
                $months[$entry->month]['sistol'] = round($entry->sistol, 2);
                $months[$entry->month]['tinggi_fundus'] = round($entry->tinggi_fundus, 2);
                $months[$entry->month]['keluhan'] = $entry->keluhan;
                $months[$entry->month]['statusperiksa'] = $entry->statusperiksa;
            }
        }


        return response()->json(array_values($months));
    }
    public function getPeriksaLast12Monthspos($id)
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
                'lila_periksa' => null,
                'lingkarperut_periksa' => null,
                'diastol' => null,
                'sistol' => null,
                'tinggi_fundus' => null,
                'keluhan' => null,
                'statusperiksa' => null,
            ];
        }

        $data = PeriksaWus::selectRaw('
        DATE_FORMAT(STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y"), "%Y-%m") as month,
        AVG(lila_periksa) as lila_periksa,
        AVG(lingkarperut_periksa) as lingkarperut_periksa,
        AVG(diastol) as diastol,
        AVG(sistol) as sistol,
        AVG(tinggi_fundus) as tinggi_fundus,
        GROUP_CONCAT(keluhan SEPARATOR ", ") as keluhan,
        statusperiksa
    ')
            ->join('kegiatan_posyandus', 'periksawuses.kegiatanposyandu_w_u_s_id', '=', 'kegiatan_posyandus.id')
            ->where('kegiatan_posyandus.posyandu_id', $id)
            ->whereRaw('STR_TO_DATE(kegiatan_posyandus.tgl_kegiatan, "%d-%m-%Y") >= ?', [$currentDate->subMonths(12)->format('Y-m-d')])
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();


        foreach ($data as $entry) {
            if (isset($months[$entry->month])) {
                $months[$entry->month]['lila_periksa'] = round($entry->lila_periksa, 2);
                $months[$entry->month]['lingkarperut_periksa'] = round($entry->lingkarperut_periksa, 2);
                $months[$entry->month]['diastol'] = round($entry->diastol, 2);
                $months[$entry->month]['sistol'] = round($entry->sistol, 2);
                $months[$entry->month]['tinggi_fundus'] = round($entry->tinggi_fundus, 2);
                $months[$entry->month]['keluhan'] = $entry->keluhan;
                $months[$entry->month]['statusperiksa'] = $entry->statusperiksa;
            }
        }


        return response()->json(array_values($months));
    }
}
