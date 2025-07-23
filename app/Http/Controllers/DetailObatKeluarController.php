<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailObat;
use App\Models\DetailObatKeluar;
use App\Models\ObatKeluar;
use App\Models\StokObat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetailObatKeluarController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_obatkeluar' => 'required|exists:obat_keluar,id_obatkeluar',
            'id_obat' => 'required|array',
            'id_obat.*' => 'required|exists:obat,id_obat',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['id_obat'] as $index => $id_obat) {
                $jumlah_dibutuhkan = $validated['jumlah'][$index];
                $sisa = $jumlah_dibutuhkan;

                $detailObatList = DetailObat::where('id_obat', $id_obat)
                    ->where('jumlah', '>', 0)
                    ->orderBy('expired', 'asc')
                    ->get();

                // Hitung stok total yang tersedia
                $stokTersedia = $detailObatList->sum('jumlah');

                foreach ($detailObatList as $detail) {
                    if ($sisa <= 0) break;

                    $jumlah_diambil = min($detail->jumlah, $sisa);

                    // Cek apakah sudah ada baris untuk id_obatkeluar + id_detailobat
                    $existing = DetailObatKeluar::where('id_obatkeluar', $request->id_obatkeluar)
                        ->where('id_detailobat', $detail->id_detailobat)
                        ->first();

                    if ($existing) {
                        // Jika sudah ada, cukup update jumlahnya
                        $existing->jumlah += $jumlah_diambil;
                        $existing->save();
                    } else {
                        // Jika belum ada, buat baru
                        DetailObatKeluar::create([
                            'id_obatkeluar' => $request->id_obatkeluar,
                            'id_detailobat' => $detail->id_detailobat,
                            'jumlah' => $jumlah_diambil,
                        ]);
                    }

                    // Kurangi stok
                    $detail->jumlah -= $jumlah_diambil;
                    $detail->save();

                    $sisa -= $jumlah_diambil;
                }

                if ($sisa > 0) {
                    DB::rollBack();
                    // Ambil nama obat
                    $obat = StokObat::find($id_obat);
                    $nama_obat = $obat ? $obat->nama : 'ID ' . $id_obat;

                    return response()->json([
                        'success' => false,
                        'message' => 'Stok untuk obat ' . $nama_obat . ' tidak mencukupi. Stok Tersedia ' . $stokTersedia,
                    ], 400);
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getObatBersedia()
    {
        $obatList = DB::table('obat')
        ->join('jenis_obat', 'obat.id_jenisobat', '=', 'jenis_obat.id_jenisobat')
        ->join('detail_obat', 'obat.id_obat', '=', 'detail_obat.id_obat')
        ->whereNull('obat.deleted_at')
        ->whereNull('detail_obat.deleted_at') // <--- tambahkan ini
        ->where('detail_obat.jumlah', '>', 0)
        ->select(
            'obat.id_obat as id',
            'obat.nama as nama_obat',
            'jenis_obat.nama as nama_jenis',
            DB::raw('SUM(detail_obat.jumlah) as total_stok')
        )
        ->groupBy('obat.id_obat', 'obat.nama', 'jenis_obat.nama')
        ->orderBy('obat.nama') // <-- urutkan berdasarkan nama obat (abjad)
        ->get();

        return response()->json($obatList);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id_obatkeluar' => 'required|exists:obat_keluar,id_obatkeluar',
            'jumlah' => 'nullable|array',
            'jumlah.*' => 'nullable|integer|min:1',
            'id_detailobatkeluar' => 'nullable|array',
            'id_detailobatkeluar.*' => 'nullable|exists:detail_obat_keluar,id_detailobatkeluar',
            'id_obat' => 'nullable|array',
            'id_obat.*' => 'nullable|exists:obat,id_obat',
        ]);

        DB::beginTransaction();

        try {
            $idDetailList = $validated['id_detailobatkeluar'] ?? [];

            $obatKeluar = ObatKeluar::with('detailObat')->find($validated['id_obatkeluar']);
            $detailObatKeluar = $obatKeluar->detailObat;

            $existingDetailIds = $detailObatKeluar->pluck('id_detailobatkeluar')->toArray();

            $requestDetailIds = array_filter($idDetailList);

            $toDeleteIds = array_diff($existingDetailIds, $requestDetailIds);

            if (!empty($toDeleteIds)) {
                $toDeleteData = DetailObatKeluar::whereIn('id_detailobatkeluar', $toDeleteIds)->get();

                $toDeleteDataIds = $toDeleteData->pluck('id_detailobat')->toArray();

                $stockMap = DetailObat::withTrashed()
                    ->whereIn('id_detailobat', $toDeleteDataIds)
                    ->get()
                    ->keyBy('id_detailobat');

                foreach ($stockMap as $stok) {
                    if ($stok->trashed()) {
                        $result = $stok->restore();
                        $stok->deleted_at = null;
                        Log::info("Restore status for {$stok->id_detailobat}: " . ($result ? 'OK' : 'FAILED'));
                    }
                }

                foreach ($toDeleteData as $deleted) {
                    $detailObat = $stockMap[$deleted->id_detailobat] ?? null;
                    if ($detailObat) {
                        $detailObat->jumlah += $deleted->jumlah;
                        $detailObat->save();
                    }
                }

                DetailObatKeluar::whereIn('id_detailobatkeluar', $toDeleteIds)->delete();
            }

            if (!$request->jumlah) {
                DB::commit();
                return response()->json(['success' => true]); 
            }

            $jumlahList = $validated['jumlah'];
            $idObatList = $validated['id_obat'];

            foreach ($idObatList as $index => $id_obat) {
                $jumlah_baru = $jumlahList[$index];

                // Ambil nama obat untuk pesan error
                $obat = StokObat::find($id_obat);
                $nama_obat = $obat ? $obat->nama : "ID $id_obat";

                if (isset($idDetailList[$index])) {
                    // ======== CASE 1: UPDATE DATA LAMA =========
                    $detail = DetailObatKeluar::find($idDetailList[$index]);
                    $jumlah_lama = $detail->jumlah;
                    $selisih = $jumlah_baru - $jumlah_lama;

                    $stok = $detail->detailObat;
                    if ($stok->jumlah < $selisih) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => "Stok obat '$nama_obat' tidak mencukupi untuk update. Stok tersedia: {$stok->jumlah}, tambahan diminta: $selisih"]);
                    }

                    $detail->jumlah = $jumlah_baru;
                    $detail->save();

                    $stok->jumlah -= $selisih;
                    $stok->save();
                } else {
                    // ======== CASE 2: TAMBAH OBAT BARU =========
                    $sisa = $jumlah_baru;

                    $detailObatList = DetailObat::where('id_obat', $id_obat)
                        ->where('jumlah', '>', 0)
                        ->orderBy('expired', 'asc')
                        ->get();

                    foreach ($detailObatList as $detail) {
                        if ($sisa <= 0) break;

                        $jumlah_diambil = min($detail->jumlah, $sisa);

                        // Cek apakah sudah ada baris untuk id_obatkeluar + id_detailobat
                    $existing = DetailObatKeluar::where('id_obatkeluar', $request->id_obatkeluar)
                        ->where('id_detailobat', $detail->id_detailobat)
                        ->first();

                    if ($existing) {
                        // Jika sudah ada, cukup update jumlahnya
                        $existing->jumlah += $jumlah_diambil;
                        $existing->save();
                    } else {
                        // Jika belum ada, buat baru
                        DetailObatKeluar::create([
                            'id_obatkeluar' => $request->id_obatkeluar,
                            'id_detailobat' => $detail->id_detailobat,
                            'jumlah' => $jumlah_diambil,
                        ]);
                    }
                        $detail->jumlah -= $jumlah_diambil;
                        $detail->save();

                        $sisa -= $jumlah_diambil;
                    }

                    if ($sisa > 0) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Stok obat '$nama_obat' tidak mencukupi. Stok tersedia: $jumlah_diambil, diminta: $jumlah_baru",
                        ], 400);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
