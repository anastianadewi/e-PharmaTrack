<?php

namespace App\Exports;

use App\Models\StokObat;
use App\Models\ObatKeluar;
use App\Models\DetailObatKeluar;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ObatKeluarExport implements FromView
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
        // Ambil data detail obat keluar berdasarkan rentang tanggal, Kemudian dengan eager loading relasi ke tabel-tabel terkait
        $obatkeluar = DetailObatKeluar::whereBetween('created_at', [$this->tgl_mulai, $this->tgl_selesai])
        ->with('detailObat.stokobat.jenisObat') // Mengambil relasi berantai sampai jenis obat
        ->get()
        ->groupBy(function ($item) {
            // Kelompokkan data berdasarkan nama obat
            return $item->detailObat->stokobat->nama ?? 'Tidak diketahui';
    })
    ->map(function ($group) {
        // Ambil elemen pertama dari setiap grup untuk mendapatkan info dasar
        $first = $group->first();
        // Ambil nama dan jenis obat dari elemen pertama
        $nama = $first->detailObat->stokobat->nama ?? '-';
        $jenis = $first->detailObat->stokobat->jenisObat->nama ?? '-';
        // Kembalikan array dengan nama lengkap dan total jumlah keluar
        return [
            'nama_lengkap' => "$nama ($jenis)",
            'total' => $group->sum('jumlah') // Jumlah total dari semua item dalam grup
        ];
    });


        return view('export.obatKeluar', [
            'detailObatKeluar' => DetailObatKeluar::whereBetween('created_at', [$this->tgl_mulai, $this->tgl_selesai])->with(['obatKeluar', 'detailObat', 'detailObat.stokobat', 'detailObat.stokobat.jenisObat'])->get(),
            'obatkeluar' => $obatkeluar,
            'user' => $this->user,
            'tgl_mulai' => $this->tgl_mulai,
            'tgl_selesai' => $this->tgl_selesai,
        ]);
    }
}
