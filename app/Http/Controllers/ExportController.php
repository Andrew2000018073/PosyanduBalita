<?php

namespace App\Http\Controllers;

use App\Models\Posyandu;
use Illuminate\Http\Request;
use App\Exports\PosyanduExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportPosyandu(Request $request, $id)
    {
        $tahun = $request->query('tahun');


        return Excel::download(new PosyanduExport($id, $tahun), 'SIP' . $tahun . '.xlsx');
    }
}
