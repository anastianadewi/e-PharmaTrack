<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\StokObat;
use App\Models\JenisObat;
use App\Models\ObatMasuk;
use App\Models\DetailObat;
use Illuminate\Http\Request;
use App\Exports\StokObatExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DetailObatKeluar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class StokObatController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->input('id_jenisobat');
        $search = $request->input('search');

        $categories = JenisObat::all();
        $jenisObatList = JenisObat::all();

        $obatQuery = StokObat::with(['detail', 'jenisobat'])
            ->when($kategori, function ($query, $kategori) {
                $query->where('id_jenisobat', $kategori);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('golongan', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('detail', function ($q2) use ($search) {
                        $q2->where('expired', 'like', "%{$search}%")
                            ->orWhere('jumlah', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jenisobat', function ($q3) use ($search) {
                        $q3->where('nama', 'like', "%{$search}%");
                    });
                });
            });

        $obatList = $obatQuery->orderBy('nama')->get();

        return view('stokobat.index', compact('obatList', 'kategori', 'categories', 'jenisObatList'));
    }

    public function store(Request $request)
    {
        try{
            $request->validate([
                'nama' => 'required|string|max:50',
                'golongan' => 'required|string|max:50',
                'keterangan' => 'required|string|max:150',
                'id_jenisobat' => 'required|exists:jenis_obat,id_jenisobat',
            ]);

            StokObat::create([
                'nama' => $request->nama,
                'golongan' => $request->golongan,
                'keterangan' => $request->keterangan,
                'id_jenisobat' => $request->id_jenisobat,
            ]);

            return redirect()->route('stokobat.index', ['id_jenisobat' => $request->id_jenisobat])->with('success', 'Obat berhasil ditambahkan.');
        }
        catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan obat: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_jenisobat' => 'required|exists:jenis_obat,id_jenisobat',
            'nama' => 'required|string|max:50',
            'golongan' => 'required|string|max:50',
            'keterangan' => 'nullable|string|max:150',
        ]);

        $obat = StokObat::findOrFail($id);
        $obat->update([
            'id_jenisobat' => $request->id_jenisobat,
            'nama' => $request->nama,
            'golongan' => $request->golongan,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('stokobat.index', ['id_jenisobat' => $request->id_jenisobat])->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroyObat($id)
    {
        $obat = StokObat::with('detail')->findOrFail($id);

        // Cek apakah masih ada detail
        if ($obat->detail->isNotEmpty()) {
            return redirect()->back()->with('error', 'Obat tidak bisa dihapus karena masih memiliki stok.');
        }

        $obat->delete();

        return redirect()->route('stokobat.index')->with('success', 'Obat berhasil dihapus.');
    }

    public function tambahStok(Request $request, $id_obat)
    {
        try{
            $request->validate([
                'jumlah' => 'required|integer|min:1',
                'expired' => 'required|date',
            ]);

            DetailObat::create([
                'id_obat' => $id_obat,
                'jumlah' => $request->jumlah,
                'expired' => $request->expired,
            ]);

            ObatMasuk::create([
                'id_obat' => $id_obat,
                'jumlah' => $request->jumlah,
                'expired' => $request->expired,
            ]);

            $obat = \App\Models\StokObat::findOrFail($id_obat);

            return redirect()->route('stokobat.index', ['id_jenisobat' => $obat->id_jenisobat])->with('success', 'Stok obat berhasil ditambahkan.');
        }
        catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan stok obat: ' . $e->getMessage());
        }
    }

    public function destroyDetail(Request $request)
    {
        try{
            $ids = $request->input('ids_to_delete');

            if (!$ids || !is_array($ids)) {
                return redirect()->back()->with('error', 'Tidak ada stok yang dipilih untuk dihapus.');
            }

            $details = DetailObat::whereIn('id_detailobat', $ids)->get();

            foreach ($details as $detail) {
                $detail->deleted_by = Auth::user()->id_user;
                $detail->save();
                $detail->delete();
            }

            return redirect()->back()->with('success', 'Stok obat berhasil dihapus.');
        }
        catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus stok obat: ' . $e->getMessage());
        }
    }

    public function terhapus(Request $request)
    {
        $search = $request->search;

        $query = DetailObat::onlyTrashed()
            ->with(['stokobat.jenisobat', 'deletedBy'])
            ->orderBy('deleted_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('jumlah', 'like', "%$search%")
                ->orWhere('expired', 'like', "%$search%")
                ->orWhereDate('deleted_at', 'like', "%$search%")
                ->orWhereHas('stokobat', function ($q1) use ($search) {
                    $q1->where('nama', 'like', "%$search%")
                        ->orWhere('golongan', 'like', "%$search%")
                        ->orWhereHas('jenisObat', function ($q2) use ($search) {
                            $q2->where('nama', 'like', "%$search%");
                        });
                })
                ->orWhereHas('deletedBy', function ($q3) use ($search) {
                    $q3->where('nama', 'like', "%$search%");
                });
            });
        }

        $data = $query->paginate(10);

        return view('obatterhapus.index', compact('data'));
    }

    public function exportPdf(Request $request)
    {
        $tanggal = $request->tanggal;

        $stokObats = StokObat::with(['jenisObat', 'detail'])->orderBy('nama')->get();

        foreach ($stokObats as $obat) {
            // Stok sekarang
            $stokSaatIni = $obat->detail->sum('jumlah');

            // Total obat keluar dari tanggal tertentu hingga sekarang
            $jumlahKeluar = DetailObatKeluar::whereHas('detailObat', function ($query) use ($obat) {
                $query->where('id_obat', $obat->id_obat);
            })
            ->whereHas('obatKeluar', function ($query) use ($tanggal) {
                $query->whereDate('created_at', '>=', $tanggal);
            })
            ->sum('jumlah');

            // Stok pada tanggal tersebut = stok sekarang + jumlah keluar setelah tanggal itu
            $obat->stok_pada_tanggal = $stokSaatIni + $jumlahKeluar;
        }

        $pdf = Pdf::loadView('export.stokobat', [
            'tanggal' => $tanggal,
            'stokObats' => $stokObats,
            'user' => Auth::user(),
        ])->setPaper('A4', 'portrait');

        return $pdf->download('laporan_stok_obat_' . $tanggal . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $tanggal = $request->tanggal;

        $stokObats = StokObat::with(['jenisObat', 'detail'])->orderBy('nama')->get();

        foreach ($stokObats as $obat) {
            $stokSaatIni = $obat->detail->sum('jumlah');

            $jumlahKeluar = DetailObatKeluar::whereHas('detailObat', function ($query) use ($obat) {
                $query->where('id_obat', $obat->id_obat);
            })
            ->whereHas('obatKeluar', function ($query) use ($tanggal) {
                $query->whereDate('created_at', '>=', $tanggal);
            })
            ->sum('jumlah');

            $obat->stok_pada_tanggal = $stokSaatIni + $jumlahKeluar;
        }

        return Excel::download(new StokObatExport($tanggal, $stokObats, Auth::user()), 'laporan_stok_obat_' . $tanggal . '.xlsx');
    }
}
