<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\JenisObat;
use Illuminate\Http\Request;

class JenisObatController extends Controller
{
    public function index(Request $request)
    {
        $jenisObat = JenisObat::all(); // ambil semua data dari tabel jenis_obat

        $search = $request->input('search');
        $jenisObat = \App\Models\JenisObat::query()
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%");
            })
            ->get();

        return view('jenisobat.index', compact('jenisObat')); // kirim ke view
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:50|regex:/^[A-Za-z\s]+$/',
            ]);

            JenisObat::create($validated);

            return redirect()->route('jenisobat.index')->with('success', 'Jenis obat berhasil ditambahkan.');
        }
        catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan jenis obat: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $jenisObat = JenisObat::findOrFail($id);

            // Cek apakah ada obat yang terkait dengan jenis obat ini
            if ($jenisObat->stokobat()->exists()) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus jenis obat yang masih digunakan oleh obat.');
            }

            $jenisObat->delete();
            return redirect()->route('jenisobat.index')->with('success', 'Jenis obat berhasil dihapus.');
        }
        catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus jenis obat: ' . $e->getMessage());
        }
    }
}
