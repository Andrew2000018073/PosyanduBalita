<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class PosyanduExport implements WithMultipleSheets
{
    protected $idPosyandu;
    protected $tahun;

    public function __construct($idPosyandu, $tahun)
    {
        $this->idPosyandu = $idPosyandu;
        $this->tahun = $tahun;
    }


    public function sheets(): array
    {
        return [
            'From 1' => new CatatanBumil($this->idPosyandu,  $this->tahun), // Sheet 1
            'From 2' => new RegBalita($this->idPosyandu,  $this->tahun), // Sheet 2
            'From 3' => new WuspusRegis($this->idPosyandu,  $this->tahun), // Sheet 3
            'From 4' => new RegBumil($this->idPosyandu,  $this->tahun), // Sheet 4
            'From 5' => new RangkumanPosyandu($this->idPosyandu,  $this->tahun), // Sheet 5
            'From 6' => new HasilKegiatan($this->idPosyandu,  $this->tahun), // Sheet 6
        ];
    }
}
