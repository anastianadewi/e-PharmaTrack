<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisObatController;
use App\Http\Controllers\StokObatController;
use App\Http\Controllers\ObatMasukController;
use App\Http\Controllers\ObatKeluarController;
use App\Http\Controllers\DetailObatKeluarController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function(){

    Route::middleware('role:nakes')->group(function(){
        Route::get('/jenisobat', [JenisObatController::class, 'index'])->name('jenisobat.index');
        Route::post('/jenisobat', [JenisObatController::class, 'store'])->name('jenisobat.store');
        Route::delete('/jenisobat/{id_jenisobat}', [JenisObatController::class, 'destroy'])->name('jenisobat.destroy');

        Route::get('/user', [UserController::class, 'index'])->name('user');
        Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
        Route::delete('/user/{id_user}', [UserController::class, 'destroy'])->name('user.destroy');
        Route::put('/user/{id_user}', [UserController::class, 'update'])->name('user.update');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/stokobat', [StokObatController::class, 'index'])->name('stokobat.index');
    Route::post('/stokobat', [StokObatController::class, 'store'])->name('stokobat.store');
    Route::put('/stokobat/{id}', [StokObatController::class, 'update'])->name('stokobat.update');
    Route::delete('/stokobat/{id}/hapus-obat', [StokObatController::class, 'destroyObat'])->name('stokobat.destroyObat');
    Route::post('/stokobat/{id_obat}/tambah-stok', [StokObatController::class, 'tambahStok'])->name('stokobat.tambahStok');
    Route::delete('/stokobat/hapus-detail', [StokObatController::class, 'destroyDetail'])->name('stokobat.destroyDetail');
    Route::get('/stokobat/export-excel', [StokObatController::class, 'exportExcel'])->name('stokobat.export.excel');
    Route::get('/laporan/stokobat/unduh', [StokObatController::class, 'exportPdf'])->name('stokobat.laporan.unduh');
    Route::get('/obat-masuk', [ObatMasukController::class, 'index'])->name('obatmasuk.index');
    Route::get('/laporanmasuk', [ObatMasukController::class, 'export'])->name('obatmasuk.export');
    Route::get('/laporanmasuk/pdf', [ObatMasukController::class, 'exportPdf'])->name('obatmasuk.export.pdf');

    Route::get('/obatkeluar', [ObatKeluarController::class, 'index'])->name('obatkeluar.index');
    Route::post('/obatkeluar', [ObatKeluarController::class, 'store'])->name('obatkeluar.store');
    Route::get('/obatkeluar/{id}', [ObatKeluarController::class, 'show'])->name('obatkeluar.show');
    Route::post('/detail-obatkeluar', [DetailObatKeluarController::class, 'store'])->name('detailobatkeluar.store');
    Route::get('/get-obat-bersedia', [DetailObatKeluarController::class, 'getObatBersedia']);
    Route::delete('/obatkeluar/{id_obatkeluar}', [ObatKeluarController::class, 'destroy'])->name('obatkeluar.destroy');
    Route::post('/obatkeluar/{id}', [ObatKeluarController::class, 'update'])->name('obatkeluar.update');
    Route::post('/detailobatkeluar/update', [DetailObatKeluarController::class, 'update'])->name('detailobatkeluar.update');
    Route::get('/laporankeluar', [ObatKeluarController::class, 'export'])->name('obatkeluar.export');
    Route::get('/laporankeluar/pdf', [ObatKeluarController::class, 'exportPdf'])->name('obatkeluar.export.pdf');

    Route::get('/obatterhapus', [StokObatController::class, 'terhapus'])->name('obatterhapus.index');

    Route::post('/profile/verify', [ProfileController::class, 'verify'])->name('profile.verify');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});