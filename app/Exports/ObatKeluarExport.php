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
    protected $tgl_mulai;
    protected $tgl_selesai;
    protected $user;

    public function __construct($tgl_mulai, $tgl_selesai, $user){
        $this->tgl_mulai=$tgl_mulai;
        $this->tgl_selesai=$tgl_selesai;
        $this->user = $user;
    }
    public function view(): View
    {
        $obatkeluar = DetailObatKeluar::whereBetween('created_at', [$this->tgl_mulai, $this->tgl_selesai])->with('detailObat.stokobat.jenisObat')
    ->get()
    ->groupBy(function ($item) {
        return $item->detailObat->stokobat->nama ?? 'Tidak diketahui';
    })
    ->map(function ($group) {
        $first = $group->first();
        $nama = $first->detailObat->stokobat->nama ?? '-';
        $jenis = $first->detailObat->stokobat->jenisObat->nama ?? '-';
        return [
            'nama_lengkap' => "$nama ($jenis)",
            'total' => $group->sum('jumlah')
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
