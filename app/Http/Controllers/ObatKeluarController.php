<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\StokObat;
use App\Models\ObatKeluar;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DetailObatKeluar;
use App\Exports\ObatKeluarExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ObatKeluarController extends Controller
{
    public function index(Request $request)
    {
        $obatList = StokObat::all();

        $query = ObatKeluar::query();

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('created_at', [
                $request->tanggal_awal . ' 00:00:00',
                $request->tanggal_akhir . ' 23:59:59'
            ]);
        }

        // Filter berdasarkan keyword pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('jenis_kelamin', 'like', "%{$search}%")
                ->orWhere('keluhan', 'like', "%{$search}%");
            });
        }

        $obatkeluar = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('obatkeluar.index', compact('obatkeluar', 'obatList'));
    }

    public function show($id)
    {
        $obatKeluar = ObatKeluar::findOrFail($id);
        $detailObat = DetailObatKeluar::with('detailObat.stokobat.jenisObat')
            ->where('id_obatkeluar', $id)
            ->get();

        return view('obatkeluar.detail', compact('obatKeluar', 'detailObat'));
    }

    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'nama' => 'required|string|max:50',
                'jenis_kelamin' => 'required|string|max:10',
                'keluhan' => 'required|string|max:100',
                'suhu_tubuh' => 'required|numeric',
                'denyut_nadi' => 'required|integer',
                'tekanan_darah' => 'required|string|max:10',
                'diagnosa' => 'required|string|max:100',
                'keterangan' => 'required|string|max:150'
            ]);

            $obatKeluar = ObatKeluar::create($validated);

            return response()->json(['id' => $obatKeluar->id_obatkeluar]);
        }
        catch (Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try{
            ObatKeluar::findOrFail($id)->delete();
            return redirect()->route('obatkeluar.index')->with('success', 'Data berhasil dihapus.');
        }
        catch (Exception $e) {
            return redirect()->route('obatkeluar.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $validated = $request->validate([
                'nama' => 'required|string|max:50',
                'jenis_kelamin' => 'required|string|max:10',
                'keluhan' => 'required|string|max:100',
                'suhu_tubuh' => 'required|numeric',
                'denyut_nadi' => 'required|integer',
                'tekanan_darah' => 'required|string|max:10',
                'diagnosa' => 'required|string|max:100',
                'keterangan' => 'nullable|string|max:150'
            ]);

            $obatKeluar = ObatKeluar::findOrFail($id);
            $obatKeluar->update($validated);

            return response()->json(['success' => 'Data berhasil diperbarui.']);
        }
        catch (Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    public function export(Request $request){
        try{
            $tgl_mulai = Carbon::parse($request->query('tgl_mulai'))->startOfDay(); // 00:00:00
            $tgl_selesai = Carbon::parse($request->query('tgl_selesai'))->endOfDay(); // 23:59:59

            //cek apakah ada data
            $count = ObatKeluar::whereBetween('created_at', [
                $tgl_mulai . ' 00:00:00',
                $tgl_selesai . ' 23:59:59'
            ])->count();

            if ($count === 0) {
                return redirect()->back()->with('error', 'Tidak ada data dalam rentang tanggal yang dipilih.');
            }

            return Excel::download(new ObatKeluarExport($tgl_mulai, $tgl_selesai, Auth::user()), 'LaporanObatKeluar.xlsx');
        }
        catch (Exception $e) {
            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        $tgl_mulai = Carbon::parse($request->query('tgl_mulai'))->startOfDay(); // 00:00:00
        $tgl_selesai = Carbon::parse($request->query('tgl_selesai'))->endOfDay(); // 23:59:59

        $detailObatKeluar = DetailObatKeluar::whereBetween('created_at', [$tgl_mulai, $tgl_selesai])
            ->with(['obatKeluar', 'detailObat.stokobat.jenisObat'])
            ->get();

        $obatkeluar = $detailObatKeluar
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

        $pdf = Pdf::loadView('export.obatkeluar', [
            'detailObatKeluar' => $detailObatKeluar,
            'obatkeluar' => $obatkeluar,
            'user' => Auth::user(),
            'tgl_mulai' => $tgl_mulai,
            'tgl_selesai' => $tgl_selesai
        ])->setPaper('a4', 'landscape');

        //cek apakah ada data
        $count = ObatKeluar::whereBetween('created_at', [
            $tgl_mulai . ' 00:00:00',
            $tgl_selesai . ' 23:59:59'
        ])->count();

        if ($count === 0) {
            return redirect()->back()->with('error', 'Tidak ada data dalam rentang tanggal yang dipilih.');
        }

        return $pdf->download('Laporan Obat Keluar.pdf');
    }
}
