<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokObat;
use App\Models\DetailObat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Obat yang sering digunakan
        $chartData = DB::table('detail_obat_keluar')
        ->join('detail_obat', 'detail_obat_keluar.id_detailobat', '=', 'detail_obat.id_detailobat')
        ->join('obat', 'detail_obat.id_obat', '=', 'obat.id_obat')
        ->select(
            'obat.nama',
            DB::raw('DATE_FORMAT(detail_obat_keluar.created_at, "%Y-%m") as bulan'),
            DB::raw('SUM(detail_obat_keluar.jumlah) as total')
        )
        ->where('detail_obat_keluar.created_at', '>=', now()->subMonths(6))
        ->groupBy('obat.nama', 'bulan')
        ->orderBy('bulan')
        ->get();

        $grouped = $chartData->groupBy('nama');
        $labels = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->format('Y-m');
        })->sort()->values();

        $dataChart = [];
        foreach ($grouped as $nama => $data) {
            $series = [];
            foreach ($labels as $label) {
                $match = $data->firstWhere('bulan', $label);
                $series[] = $match ? (int)$match->total : 0;
            }
            $dataChart[] = [
                'label' => $nama,
                'data' => $series,
            ];
        }

        //prediksi 6 bulan kedepan
        $prediksi = DB::table('detail_obat_keluar')
        ->join('detail_obat', 'detail_obat_keluar.id_detailobat', '=', 'detail_obat.id_detailobat')
        ->join('obat', 'detail_obat.id_obat', '=', 'obat.id_obat')
        ->join('jenis_obat', 'obat.id_jenisobat', '=', 'jenis_obat.id_jenisobat')
        ->select(
            'obat.id_obat',
            'obat.nama as nama_obat',
            'jenis_obat.nama as jenis',
            DB::raw('SUM(detail_obat_keluar.jumlah) as total')
        )
        ->where('detail_obat_keluar.created_at', '>=', now()->subMonths(6))
        ->groupBy('obat.id_obat', 'obat.nama', 'jenis_obat.nama')
        ->orderByDesc('total')
        ->get()
        ->map(function ($item) {
            // Hitung jumlah bulan yang ada transaksi (data tidak nol)
            $jumlah_bulan_terpakai = DB::table('detail_obat_keluar')
            ->join('detail_obat', 'detail_obat_keluar.id_detailobat', '=', 'detail_obat.id_detailobat')
            ->where('detail_obat.id_obat', $item->id_obat)
            ->where('detail_obat_keluar.created_at', '>=', now()->subMonths(6))
            ->select(DB::raw('DATE_FORMAT(detail_obat_keluar.created_at, "%Y-%m") as bulan'))
            ->groupBy(DB::raw('DATE_FORMAT(detail_obat_keluar.created_at, "%Y-%m")'))
            ->get()
            ->count(); // ← gunakan get()->count() karena count() langsung menghitung baris dari hasil grouped

            $rata = $jumlah_bulan_terpakai > 0 ? round($item->total / $jumlah_bulan_terpakai) : 0;

            return [
                'nama' => $item->nama_obat,
                'jenis' => $item->jenis,
                'total' => $item->total,
                'rata' => $rata,
                'prediksi' => $rata * 6,
            ];
        });

        // Stok habis atau hampir habis
        $stokAlert = StokObat::with(['detail', 'jenisObat'])->get()->map(function ($obat) {
            $total = $obat->detail->sum('jumlah');
            $status = $total == 0 ? 'Habis' : ($total <= 9 ? 'Hampir Habis' : 'Aman');
            return [
                'id' => $obat->id_obat,
                'nama' => $obat->nama,
                'jenis' => $obat->jenisObat->nama,
                'stok' => $total,
                'status' => $status,
            ];
        })->filter(fn ($o) => in_array($o['status'], ['Habis', 'Hampir Habis']));

        // Stok Expired
        $today = Carbon::now();
        $expiredAlert = DetailObat::with('stokobat.jenisObat')->get()->map(function ($detail) use ($today) {
            $exp = Carbon::parse($detail->expired);
            $selisih = $today->diffInDays($exp, false);
            $status = $selisih < 0 ? 'Expired' : ($selisih <= 30 ? 'Hampir Expired' : null);

            if ($status) {
                return [
                    'id' => $detail->stokobat->id_obat,
                    'nama' => $detail->stokobat->nama,
                    'jenis' => $detail->stokobat->jenisObat->nama,
                    'stok' => $detail->jumlah,
                    'expired' => $exp->translatedFormat('d F Y'),
                    'status' => $status,
                ];
            }

            return null;
        })->filter();

        return view('dashboard', [
            'chartData' => $dataChart,
            'labels' => $labels,
            'prediksi' => $prediksi,
            'stokAlert' => $stokAlert,
            'expiredAlert' => $expiredAlert,
        ]);
    }
}
