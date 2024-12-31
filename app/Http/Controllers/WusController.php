<?php

namespace App\Http\Controllers;

use App\Models\Wus;
use App\Models\PendaftaranIbuHamil;
use App\Models\Kehamilan;
use App\Models\periksawus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WusController extends Controller
{
    public function getOrtu()
    {
        $data = Wus::with('PUS:nama_suami')->get();
        // $cars = Car::with('brand:name,id')->get();

        // Return as JSON
        return response()->json($data);
    }
    public function getWus()
    {
        // Step 1: Retrieve all WUS with their latest completed periksawus data along with posyandu
        $wusWithLatestLila = Wus::with(['periksawus' => function ($query) {
            $query->whereHas('Kegiatanposyandu', function ($subQuery) {
                $subQuery->where('status_kegiatan', 'selesai');
            })->latest('created_at')->take(1);
        }, 'posyandu'])->get(); // Include posyandu relationship

        // Step 2: Initialize an array to hold the latest Lila data
        $latestLilaData = [];

        // Step 3: Loop through each WUS to gather data
        foreach ($wusWithLatestLila as $wus) {
            // Get the latest periksawus entry
            $latestPeriksa = $wus->periksawus->first();

            // Check for lila_periksa data
            $lilaTerbaru = $latestPeriksa ? $latestPeriksa->lila_periksa : null;

            // If lila_periksa is null, backtrack to find the latest non-null entry
            if (is_null($lilaTerbaru)) {
                $lilaTerbaru = $this->getLatestLilaFromPreviousActivities($wus->id);
            }

            // Prepare the response data with Wus and Posyandu details
            $latestLilaData[] = [
                'wus_id' => $wus->id,
                'nama' => $wus->nama,
                'lila_periksa' => $lilaTerbaru,
                'lila_wus' => $wus->lila_wus,
                'tanggal_lahir' => $wus->tanggal_lahir,
                'statuswus' => $wus->statuswus,
                'posyandus_id' => $wus->posyandus_id,
                'posyandu_nama' => optional($wus->posyandu)->nama_posyandu, // Safely access posyandu name
            ];
        }

        // Step 4: Return the latest Lila data as JSON
        return response()->json($latestLilaData);
    }

    public function getWusPosyandu($id)
    {
        // Step 1: Retrieve all WUS with their latest completed periksawus data along with posyandu
        $wusWithLatestLila = Wus::with(['periksawus' => function ($query) {
            $query->whereHas('Kegiatanposyandu', function ($subQuery) {
                $subQuery->where('status_kegiatan', 'selesai');
            })->latest('created_at')->take(1);
        }, 'posyandu'])->where('posyandus_id', $id)->get(); // Include posyandu relationship

        // Step 2: Initialize an array to hold the latest Lila data
        $latestLilaData = [];

        // Step 3: Loop through each WUS to gather data
        foreach ($wusWithLatestLila as $wus) {
            // Get the latest periksawus entry
            $latestPeriksa = $wus->periksawus->first();

            // Check for lila_periksa data
            $lilaTerbaru = $latestPeriksa ? $latestPeriksa->lila_periksa : null;

            // If lila_periksa is null, backtrack to find the latest non-null entry
            if (is_null($lilaTerbaru)) {
                $lilaTerbaru = $this->getLatestLilaFromPreviousActivities($wus->id);
            }

            // Prepare the response data with Wus and Posyandu details
            $latestLilaData[] = [
                'wus_id' => $wus->id,
                'nama' => $wus->nama,
                'lila_periksa' => $lilaTerbaru,
                'tanggal_lahir' => $wus->tanggal_lahir,
                'statuswus' => $wus->statuswus,
                'posyandu_id' => $wus->posyandus_id,
                'posyandu_nama' => optional($wus->posyandu)->nama_posyandu, // Safely access posyandu name
            ];
        }

        // Step 4: Return the latest Lila data as JSON
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
        // Add validation to ensure data integrity
        Wus::create([
            'nama' => $request->nama,
            'lila_wus' => $request->lila_wus,
            'tanggal_lahir' => $request->tanggal_lahir,
            // 'kehamilan_id'=> $request->kehamilan_id,
            'posyandus_id' => $request->posyandus_id,
            'statuswus' => $request->statuswus,
        ]);
    }
    public function updateWus(Request $request)
    {
        // Add validation to ensure data integrity
        $updated = Wus::where('id', $request->id)->update([
            'nama' => $request->nama,
            'lila_wus' => $request->lila_wus,
            'tanggal_lahir' => $request->tanggal_lahir,
            // 'kehamilan_id'=> $request->kehamilan_id,
            'statuswus' => $request->statuswus,
            'posyandus_id' => $request->posyandus_id,
        ]);

        return response()->json($updated ? 'Update successful' : 'Update failed');
    }
    public function updateWusPeserta(Request $request)
    {
        // Add validation to ensure data integrity
        $updated = Wus::where('id', $request->wus_id)->update([
            'statuswus' => $request->statuswus,
            'lila_wus' => $request->lila_periksa,
        ]);

        return response()->json($updated ? 'Update successful' : 'Update failed');
    }
    public function storeWusA(Request $request)
    {
        // Add validation to ensure data integrity

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
        // Add validation to ensure data integrity


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
        // Step 1: Retrieve all WUS with their latest completed periksawus data
        $wusWithLatestlingper = Wus::with(['periksawus' => function ($query) {
            $query->whereHas('Kegiatanposyandu', function ($subQuery) {
                $subQuery->where('status_kegiatan', 'selesai');
            })->latest('created_at')->take(1);
        }])->get();

        // Step 2: Initialize an array to hold the latest Lila data
        $latestLiperData = [];

        // Step 3: Loop through each WUS to gather data
        foreach ($wusWithLatestlingper as $wus) {
            // Get the latest periksawus entry
            $latestPeriksa = $wus->periksawus->first();

            // Initialize the lingkarperut to null in case no data is found
            $lingkarperut = null;

            // Check for lingkarperut_periksa data
            if ($latestPeriksa) {
                $lingkarperut = $latestPeriksa->lingkarperut_periksa;
            }

            // If lingkarperut_periksa is null, backtrack to find the latest non-null entry
            if (is_null($lingkarperut)) {
                $lingkarperut = $this->getLatestLilaFromPreviousActivities($wus->id);
            }

            // Prepare the response data
            $latestLiperData[] = [
                'wus_id' => $wus->id,
                'nama' => $wus->nama,
                'lingkarperut_periksa' => $lingkarperut,
            ];
        }

        // Step 4: Return the latest Lila data as JSON
        return response()->json($latestLiperData);
    }

    public function getGemukPos($id)
    {
        // Step 1: Retrieve all WUS with their latest completed periksawus data
        $wusWithLatestlingper = Wus::with(['periksawus' => function ($query) {
            $query->whereHas('Kegiatanposyandu', function ($subQuery) {
                $subQuery->where('status_kegiatan', 'selesai');
            })->latest('created_at')->take(1);
        }])->where('posyandus_id', $id)->get();

        // Step 2: Initialize an array to hold the latest Lila data
        $latestLilaData = [];

        // Step 3: Loop through each WUS to gather data
        foreach ($wusWithLatestlingper as $wus) {
            // Get the latest periksawus entry
            $latestPeriksa = $wus->periksawus->first();

            // Initialize the lingkarperut to null in case no data is found
            $lingkarperut = null;

            // Check for lingkarperut_periksa data
            if ($latestPeriksa) {
                $lingkarperut = $latestPeriksa->lingkarperut_periksa;
            }

            // If lingkarperut_periksa is null, backtrack to find the latest non-null entry
            if (is_null($lingkarperut)) {
                $lingkarperut = $this->getLatestLilaFromPreviousActivities($wus->id);
            }

            // Prepare the response data
            $latestLilaData[] = [
                'wus_id' => $wus->id,
                'nama' => $wus->nama,
                'lingkarperut_periksa' => $lingkarperut,
            ];
        }

        // Step 4: Return the latest Lila data as JSON
        return response()->json($latestLilaData);
    }


    public function getKek()
    {
        // Step 1: Retrieve all WUS with their latest completed periksawus data
        $wusWithLatestLila = Wus::with(['periksawus' => function ($query) {
            $query->whereHas('Kegiatanposyandu', function ($subQuery) {
                $subQuery->where('status_kegiatan', 'selesai');
            })->latest('created_at')->take(1);
        }])->where('statuswus', 'Hamil')->get();

        // Step 2: Initialize an array to hold the latest Lila data
        $latestLilaData = [];

        // Step 3: Loop through each WUS to gather data
        foreach ($wusWithLatestLila as $wus) {
            // Get the latest periksawus entry
            $latestPeriksa = $wus->periksawus->first();

            // Check for lila_periksa data
            $lilaTerbaru = $latestPeriksa ? $latestPeriksa->lila_periksa : null;

            // If lila_periksa is null, backtrack to find the latest non-null entry
            if (is_null($lilaTerbaru)) {
                $lilaTerbaru = $this->getLatestLilaFromPreviousActivities($wus->id);
            }

            // Prepare the response data
            $latestLilaData[] = [
                'wus_id' => $wus->id,
                'nama' => $wus->nama,
                'lila_periksa' => $lilaTerbaru,
            ];
        }

        // Step 4: Return the latest Lila data as JSON
        return response()->json($latestLilaData);
    }
    public function getKekPos($id)
    {
        // Step 1: Retrieve all WUS with their latest completed periksawus data for the given posyandu
        $wusWithLatestLila = Wus::with(['periksawus' => function ($query) {
            $query->whereHas('Kegiatanposyandu', function ($subQuery) {
                $subQuery->where('status_kegiatan', 'selesai');
            })->latest('created_at')->take(1);
        }])
            ->where('posyandus_id', $id)->where('statuswus', 'Hamil') // Filter WUS by posyandus_id
            ->get();

        // Step 2: Initialize an array to hold the latest Lila data
        $latestLilaData = [];

        // Step 3: Loop through each WUS to gather data
        foreach ($wusWithLatestLila as $wus) {
            // Get the latest periksawus entry
            $latestPeriksa = $wus->periksawus->first();

            // Check for lila_periksa data
            $lilaTerbaru = $latestPeriksa ? $latestPeriksa->lila_periksa : $this->getLatestLilaFromPreviousActivities($wus->id);

            // Only include WUS with a valid lila_periksa
            if (!is_null($lilaTerbaru)) {
                $latestLilaData[] = [
                    'wus_id' => $wus->id,
                    'nama' => $wus->nama,
                    'lila_periksa' => $lilaTerbaru,
                ];
            }
        }

        // Step 4: Return the latest Lila data as JSON
        return response()->json($latestLilaData);
    }




    // Helper function to retrieve the latest available lila_periksa from previous activities
    private function getLatestLilaFromPreviousActivities($wusId)
    {
        // Get the most recent periksawus entry with a completed kegiatan
        $periksaWus = PeriksaWus::where('wus_id', $wusId)
            ->whereHas('Kegiatanposyandu', function ($query) {
                $query->where('status_kegiatan', 'selesai');
            })
            ->latest('created_at')
            ->first();

        // Return the lila_periksa value or null if not found
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

        // Prepare the months array
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $monthIndex = intval($date->format('m'));
            $months[$date->format('Y-m')] = [
                'KEK' => 0,
                'Normal' => 0,
                'month' => $monthsNames[$monthIndex] // Store month name
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

        // Populate the months array with the fetched data
        // Populate the months array with the fetched data
        foreach ($data as $entry) {
            if (isset($months[$entry->month])) {
                $months[$entry->month]['KEK'] = $entry->KEK;
                $months[$entry->month]['Normal'] = $entry->Normal;
            }
        }

        // Ensure every month has a label
        foreach ($months as $month => &$values) {
            if (!isset($values['month'])) {
                $values['month'] = $monthsNames[(int)date('m', strtotime($month))]; // Map back the month name
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

        // Prepare the months array
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $monthIndex = intval($date->format('m'));
            $months[$date->format('Y-m')] = [
                'KEK' => 0,
                'Normal' => 0,
                'month' => $monthsNames[$monthIndex] // Store month name
            ];
        }

        // Execute the query with proper date conversion
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

        // Populate the months array with the fetched data
        // Populate the months array with the fetched data
        foreach ($data as $entry) {
            if (isset($months[$entry->month])) {
                $months[$entry->month]['KEK'] = $entry->KEK;
                $months[$entry->month]['Normal'] = $entry->Normal;
            }
        }

        // Ensure every month has a label
        foreach ($months as $month => &$values) {
            if (!isset($values['month'])) {
                $values['month'] = $monthsNames[(int)date('m', strtotime($month))]; // Map back the month name
            }
        }

        return response()->json(array_values($months));
    }

    public function getDetail($id)
    {
        // Use the $id directly from the route
        $posyandu = Wus::where('id', $id)->first(); // Use first() to get a single record
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

        // Prepare the months array for the last 12 months with month names
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthIndex = (int)$date->format('m') - 1; // Adjust index for $monthsNames array

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

        // Retrieve and process the data from the database
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

        // Populate the months array with data
        foreach ($data as $entry) {
            if (isset($months[$entry->month])) {
                $months[$entry->month]['lila_periksa'] = round($entry->lila_periksa, 2);
                $months[$entry->month]['lingkarperut_periksa'] = round($entry->lingkarperut_periksa, 2);
                $months[$entry->month]['diastol'] = round($entry->diastol, 2);
                $months[$entry->month]['sistol'] = round($entry->sistol, 2);
                $months[$entry->month]['tinggi_fundus'] = round($entry->tinggi_fundus, 2);
                $months[$entry->month]['keluhan'] = $entry->keluhan; // Keluhan tidak perlu pembulatan
                $months[$entry->month]['statusperiksa'] = $entry->statusperiksa; // Status tidak perlu pembulatan
            }
        }


        return response()->json(array_values($months));
    }
    public function getPeriksaLast12Months($id)
    {
        $currentDate = now();
        $monthsNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Prepare the months array for the last 12 months with month names
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthIndex = (int)$date->format('m') - 1; // Adjust index for $monthsNames array

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

        // Retrieve and process the data from the database
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

        // Populate the months array with data
        foreach ($data as $entry) {
            if (isset($months[$entry->month])) {
                $months[$entry->month]['lila_periksa'] = round($entry->lila_periksa, 2);
                $months[$entry->month]['lingkarperut_periksa'] = round($entry->lingkarperut_periksa, 2);
                $months[$entry->month]['diastol'] = round($entry->diastol, 2);
                $months[$entry->month]['sistol'] = round($entry->sistol, 2);
                $months[$entry->month]['tinggi_fundus'] = round($entry->tinggi_fundus, 2);
                $months[$entry->month]['keluhan'] = $entry->keluhan; // Keluhan tidak perlu pembulatan
                $months[$entry->month]['statusperiksa'] = $entry->statusperiksa; // Status tidak perlu pembulatan
            }
        }


        return response()->json(array_values($months));
    }
    public function getPeriksaLast12Monthspos($id)
    {
        $currentDate = now();
        $monthsNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Prepare the months array for the last 12 months with month names
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthIndex = (int)$date->format('m') - 1; // Adjust index for $monthsNames array

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

        // Retrieve and process the data from the database
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


        // Populate the months array with data
        foreach ($data as $entry) {
            if (isset($months[$entry->month])) {
                $months[$entry->month]['lila_periksa'] = round($entry->lila_periksa, 2);
                $months[$entry->month]['lingkarperut_periksa'] = round($entry->lingkarperut_periksa, 2);
                $months[$entry->month]['diastol'] = round($entry->diastol, 2);
                $months[$entry->month]['sistol'] = round($entry->sistol, 2);
                $months[$entry->month]['tinggi_fundus'] = round($entry->tinggi_fundus, 2);
                $months[$entry->month]['keluhan'] = $entry->keluhan; // Keluhan tidak perlu pembulatan
                $months[$entry->month]['statusperiksa'] = $entry->statusperiksa; // Status tidak perlu pembulatan
            }
        }


        return response()->json(array_values($months));
    }
}
