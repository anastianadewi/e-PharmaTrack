<?php

namespace App\Exports;

use App\Models\JenisObat;
use App\Models\StokObat;
use App\Models\ObatMasuk;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class ObatMasukExport implements FromView
{
    // Properti untuk menyimpan tanggal awal, tanggal akhir, dan user yang melakukan export
    protected $tgl_mulai;
    protected $tgl_selesai;
    protected $user;

    // Konstruktor untuk mengisi properti dari parameter saat class dipanggil
    public function __construct($tgl_mulai, $tgl_selesai, $user){
        $this->tgl_mulai=$tgl_mulai;
        $this->tgl_selesai=$tgl_selesai;
        $this->user = $user;
    }
    public function view(): View
    {
        // Ambil data obat masuk berdasarkan rentang tanggal created_at, Kemudian load relasi stokobat dan jenis_obat (eager loading agar lebih efisien)
        $obatmasuk = ObatMasuk::whereBetween('created_at', [$this->tgl_mulai, $this->tgl_selesai])
        ->with('stokobat.jenisObat')
        ->get()
        // Kelompokkan data berdasarkan nama jenis obat
        ->groupBy(function ($item) {
            return data_get($item, 'stokobat.jenisObat.nama', 'Tidak diketahui');
        });

        return view('export.obatMasuk', [
            'obatmasuk' => $obatmasuk,
            'user' => $this->user,
            'tgl_mulai' => $this->tgl_mulai,
            'tgl_selesai' => $this->tgl_selesai,
        ]);
    }
}
