<?php

use App\Models\periksawus;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PUSController;
use App\Http\Controllers\WusController;
use App\Http\Controllers\BayiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\KehamilanController;
use App\Http\Controllers\PeriksawusController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PeriksaBalitaController;
use App\Http\Controllers\TambahPosyanduController;
use App\Http\Controllers\AlatKontrasepsiController;
use App\Http\Controllers\ImunisasiBalitaController;
use App\Http\Controllers\KegiatanPosyanduController;
use App\Http\Controllers\PivotKontrasepsiPusController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->get('/user/roles', function (Request $request) {
    return response()->json([
        'roles' => $request->user()->getRoleNames(),
        'permissions' => $request->user()->getAllPermissions()->pluck('name')
    ]);
});


Route::post('/login', [LoginController::class, 'index']);
Route::middleware('auth:sanctum')->post('/logout', [LoginController::class, 'logout']);
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/roles', [RegisterController::class, 'getRoles']);
Route::get('/getusers', [UserController::class, 'getAllUsersWithRoles']);
Route::post('/users', [UserController::class, 'store']); // API untuk menambahkan pengguna
Route::get('/roles', [UserController::class, 'getRoles']); // API untuk mengambil daftar roles
Route::put('updatePeserta/{id}', [UserController::class, 'updatePeserta']);
Route::post('deleteUser/{id}', [UserController::class, 'deleteUser']);


// Posyandu
Route::post('storePosyandu', [TambahPosyanduController::class, 'storePosyandu']);
Route::get('getPosyandu', [TambahPosyanduController::class, 'getPosyandu']);
Route::get('detail-posyandu/{id}', [TambahPosyanduController::class, 'getDetail']);
Route::post('updatePosyandu/{id}', [TambahPosyanduController::class, 'updatePosyandu']);
Route::post('deletePosyandu/{id}', [TambahPosyanduController::class, 'deletePosyandu']);

//Kegiatan
Route::get('getKegiatan/{id}', [KegiatanPosyanduController::class, 'getKegiatan']);
Route::get('getKegiatanActive/{id}', [KegiatanPosyanduController::class, 'getKegiatanActive']);
Route::post('storeKegiatan', [KegiatanPosyanduController::class, 'storeKegiatan']);
Route::post('updateKegiatan/{id}', [KegiatanPosyanduController::class, 'updateKegiatan']);
Route::post('deleteKegiatan/{id}', [KegiatanPosyanduController::class, 'deleteKegiatan']);
Route::get('getStatus/{id}', [KegiatanPosyanduController::class, 'getStatus']);

// WUS
Route::post('storeWusMenyusui', [WusController::class, 'storeWusA']);
Route::post('StoreWusHamil', [WusController::class, 'storeWusHamil']);
Route::get('getWus', [WusController::class, 'getWus']);
Route::get('getWusPeserta/{id}', [WusController::class, 'getWusPeserta']);
Route::get('getOrtu', [WusController::class, 'getOrtu']);
Route::post('storeWus', [WusController::class, 'storeWus']);
Route::post('deleteWus/{id}', [WusController::class, 'deleteWus']);
Route::post('updateWus/{id}', [WusController::class, 'updateWus']);
Route::post('updateWusPeserta/{id}', [WusController::class, 'updateWusPeserta']);
Route::get('getWusStatus', [WusController::class, 'getWusStatus']);
Route::get('getJumlahWus', [WusController::class, 'getJumlahWus']);
Route::get('getJumlahWusPosyandu/{id}', [WusController::class, 'getJumlahWusPosyandu']);


// Kegiatan WUS
Route::post('storePesertaWus', [PeriksawusController::class, 'storePesertaWus']);
Route::get('datawus/{id}', [PeriksawusController::class, 'getPesertaWus']);
Route::get('getPesertaWus/{id}', [PeriksawusController::class, 'getPesertaActiveWus']);
Route::get('getNamaWus/{id}', [PeriksawusController::class, 'getNamaPeserta']);
Route::post('deletePesertaWus/{id}', [PeriksawusController::class, 'deletePesertaWus']);
Route::post('updatePesertaWus/{id}', [PeriksawusController::class, 'updatePesertaWus']);

// PUS
Route::get('getPus', [PUSController::class, 'getPus']);
Route::post('storePus', [PUSController::class, 'storePus']);
Route::post('updatePus/{id}', [PUSController::class, 'updatePus']);
Route::post('deletePus/{id}', [PUSController::class, 'deletePus']);
Route::get('detail-puses/{id}', [PUSController::class, 'getDetail']);

// Bayi
Route::post('storeBayi', [BayiController::class, 'storeBayi']);
Route::post('updateBayis/{id}', [BayiController::class, 'updateBayis']);
Route::get('getBayi', [BayiController::class, 'getBayi']);
Route::get('getBayiPosyandu/{id}', [BayiController::class, 'getBayiPosyandu']);
Route::get('getpesertabalita/{id}', [BayiController::class, 'getpesertabalita']);

Route::get('getAnak/{id}', [BayiController::class, 'getAnak']);
Route::post('deleteBayi/{id}', [BayiController::class, 'deleteBayi']);
Route::get('getJumlahBalitaPosyandu/{id}', [BayiController::class, 'getJumlahBalitaPosyandu']);

// Kegiatan Balita
Route::get('databalita/{id}', [PeriksaBalitaController::class, 'getPesertaBalita']);
Route::get('getPesertaBalita/{id}', [PeriksaBalitaController::class, 'getPesertaActiveBalita']);
Route::get('getNamaBayi/{id}', [PeriksaBalitaController::class, 'getNamaPeserta']);
Route::post('deletePesertaBalita/{id}', [PeriksaBalitaController::class, 'deletePesertaBalita']);
Route::post('storePesertaBalita', [PeriksaBalitaController::class, 'storePesertaBalita']);
Route::post('updatePesertaBalita/{id}', [PeriksaBalitaController::class, 'updatePesertaBalita']);
// Route::get('datawus/{id}', [PeriksawusController::class, 'getPesertaWus']);

// Imunisasi
Route::get('getImunisasiBayi', [ImunisasiBalitaController::class, 'getImunisasiBalitas']);
Route::get('getImunisasiBayiUpdate/{id}', [ImunisasiBalitaController::class, 'getImunisasiBalitasUpdate']);
Route::post('deleteImunisasi/{id}', [ImunisasiBalitaController::class, 'deleteImunisasi']);
Route::post('updateImunisasi/{id}', [ImunisasiBalitaController::class, 'updateImunisasi']);
Route::post('storeImunisasi', [ImunisasiBalitaController::class, 'storeImunisasi']);

// Kehamilan
Route::get('getKehamilan', [KehamilanController::class, 'getKehamilan']);
Route::get('getKehamilanUpdate/{id}', [KehamilanController::class, 'getKehamilanUpdate']);
Route::post('deleteKehamilan/{id}', [KehamilanController::class, 'deleteKehamilan']);
Route::post('updateKehamilan/{id}', [KehamilanController::class, 'updateKehamilan']);
Route::post('storeKehamilan', [KehamilanController::class, 'storeKehamilan']);

// Kontrasepsi
Route::get('getalatkontrasepsi', [AlatkontrasepsiController::class, 'getAlatkontrasepsi']);
Route::get('getAlatpakai/{id}', [PivotKontrasepsiPusController::class, 'getAlatPakai']);
Route::post('deleteKontrasepsiPakai/{id}', [PivotKontrasepsiPusController::class, 'deleteKontrasepsiPakai']);
Route::post('selesaiPakai/{id}', [PivotKontrasepsiPusController::class, 'selesaiPakai']);

Route::post('storepemakaiankontrasepsi', [PUSController::class, 'storepemakaiankontrasepsi']);

// Chart
Route::get('getAbsensi/{id}', [KegiatanPosyanduController::class, 'getAbsensi']);
Route::get('getPesertabulanan/{id}', [KegiatanPosyanduController::class, 'getPesertabulanan']);


// Laporan WUS
Route::get('getWusPosyandu/{id}', [WUSController::class, 'getWusPosyandu']);
Route::get('getGemuk', [WUSController::class, 'getGemuk']);
Route::get('getGemukPos/{id}', [WUSController::class, 'getGemukPos']);
Route::get('getKek', [WUSController::class, 'getKek']);
Route::get('getKekPos/{id}', [WUSController::class, 'getKekPos']);
Route::get('getKekLast12Months', [WUSController::class, 'getKekLast12Months']);
Route::get('getKekLast12MonthsPos/{id}', [WUSController::class, 'getKekLast12MonthsPos']);
Route::get('getWusStatusPos/{id}', [WusController::class, 'getWusStatusPos']);


// Detail WUS
Route::get('detail-wus/{id}', [WusController::class, 'getDetail']);
Route::get('getPusid/{id}', [PUSController::class, 'getPusid']);
Route::get('getPeriksaLast12Months/{id}', [WUSController::class, 'getPeriksaLast12Months']);
Route::get('getPeriksaLast12Monthsall', [WUSController::class, 'getPeriksaLast12Monthsall']);
Route::get('getPeriksaLast12Monthspos/{id}', [WUSController::class, 'getPeriksaLast12Monthspos']);
Route::get('getHamil/{id}', [KehamilanController::class, 'getHamil']);
Route::post('updateHamil/{id}', [KehamilanController::class, 'updateHamil']);
Route::post('deleteHamil/{id}', [KehamilanController::class, 'deleteHamil']);


//Dashboard
Route::get('getJumlahBalita', [BayiController::class, 'getJumlahBalita']);
Route::get('getJumlahPus', [PUSController::class, 'getJumlahPus']);
Route::get('getJumlahKegiatanbulanini', [KegiatanPosyanduController::class, 'getKegiatanBulanIni']);
Route::get('getKegiatanlast5', [KegiatanPosyanduController::class, 'getKegiatanlast5']);


// Laporan Balita
Route::get('getberatbadanumur', [BayiController::class, 'getberatbadanumur']);
Route::get('getberatbadanumurpos/{id}', [BayiController::class, 'getberatbadanumurpos']);
Route::get('gettinggibadanumur', [BayiController::class, 'gettinggibadanumur']);
Route::get('gettinggibadanumurpos/{id}', [BayiController::class, 'gettinggibadanumurpos']);
Route::get('getberatbadantinggi', [BayiController::class, 'getberatbadantinggi']);
Route::get('getberatbadantinggiPos/{id}', [BayiController::class, 'getberatbadantinggiPos']);
Route::get('getPeriksaLast12Monthsbalita', [BayiController::class, 'getPeriksaLast12Monthsbalita']);
Route::get('getPeriksaLast12MonthsbalitaPOS/{id}', [BayiController::class, 'getPeriksaLast12MonthsbalitaPOS']);


//Ekspor
// Route::get('/export/{id}', [ExportController::class, 'exportPosyandu'])->name('export.posyandu');
Route::get('/export/posyandu/{id}', [ExportController::class, 'exportPosyandu']);
