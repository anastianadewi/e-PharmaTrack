<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\ObatMasuk;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ObatMasukExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ObatMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = ObatMasuk::with('stokobat.jenisObat');

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('created_at', [
                $request->tanggal_awal . ' 00:00:00',
                $request->tanggal_akhir . ' 23:59:59'
            ]);
        }

        $data = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('obatmasuk.index', compact('data', 'request'));
    }
    
    public function export(Request $request)
    {
        try {
            $tgl_mulai = Carbon::parse($request->query('tgl_mulai'))->startOfDay(); // 00:00:00
            $tgl_selesai = Carbon::parse($request->query('tgl_selesai'))->endOfDay(); // 23:59:59

            // Cek apakah ada data
            $count = ObatMasuk::whereBetween('created_at', [
                $tgl_mulai . ' 00:00:00',
                $tgl_selesai . ' 23:59:59'
            ])->count();

            if ($count === 0) {
                return redirect()->back()->with('error', 'Tidak ada data dalam rentang tanggal yang dipilih.');
            }

            return Excel::download(new ObatMasukExport($tgl_mulai, $tgl_selesai, Auth::user()), 'LaporanObatMasuk.xlsx');
        }
        catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        $tgl_mulai = Carbon::parse($request->query('tgl_mulai'))->startOfDay(); // 00:00:00
        $tgl_selesai = Carbon::parse($request->query('tgl_selesai'))->endOfDay(); // 23:59:59

        // Ambil data
        $data = ObatMasuk::with(['stokobat.jenisObat'])
            ->whereBetween('created_at', [$tgl_mulai, $tgl_selesai])
            ->get()
            ->groupBy(function ($item) {
                return $item->stokobat->jenisObat->nama ?? 'Lainnya';
            });

        // Kirim ke view dengan nama variabel $obatmasuk
        $pdf = Pdf::loadView('export.obatmasuk', [
            'obatmasuk' => $data,
            'user' => Auth::user(),
            'tgl_mulai' => $tgl_mulai,
            'tgl_selesai' => $tgl_selesai
        ]);

        // Cek apakah ada data
        $count = ObatMasuk::whereBetween('created_at', [
            $tgl_mulai . ' 00:00:00',
            $tgl_selesai . ' 23:59:59'
        ])->count();

        if ($count === 0) {
            return redirect()->back()->with('error', 'Tidak ada data dalam rentang tanggal yang dipilih.');
        }

        return $pdf->download('Laporan Obat Masuk.pdf');
    }
}

