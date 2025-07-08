@extends('layouts.app')

<!-- @section('title', 'Pemasukan Obat') -->

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mt-3 mb-4 ms-2">
        <h4 class="fw-bold" style="color: #0050A5;">Obat Masuk</h4>
    </div>

    <form method="GET" action="{{ route('obatmasuk.index') }}">
        <div class="row g-3 align-items-end mt-3 mb-4">
            <!-- Filter Tanggal -->
            <div class="col-md-3">
                <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" id="tgl_mulai" class="form-control" value="{{ request('tanggal_awal') }}">
            </div>
            <div class="col-md-3">
                <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" id="tgl_selesai" class="form-control" value="{{ request('tanggal_akhir') }}">
            </div>

            <!-- Tombol Aksi -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Tampilkan</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('obatmasuk.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
            <div class="dropdown col-md-2">
                <button class="btn btn-success dropdown-toggle w-100" type="button" id="dropdownUnduh" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i> Unduh Laporan
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownUnduh">
                    <li><a class="dropdown-item" href="#" id="unduh-excel">Excel</a></li>
                    <li><a class="dropdown-item" href="#" id="unduh-pdf">PDF</a></li>
                </ul>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead style="border-bottom: 2px solid #333;">
                <tr>
                    <th>No</th>
                    <th>Jenis</th>
                    <th>Golongan</th>
                    <th>Nama</th>
                    <th>Jumlah</th>
                    <th>Expired</th>
                    <th>Tanggal Masuk</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $key => $item)
                    <tr>
                        <td>{{ $data->firstItem() + $loop->index }}</td>
                        <td>{{ $item->stokobat->jenisObat->nama ?? '-' }}</td>
                        <td>{{ $item->stokobat->golongan }}</td>
                        <td>{{ $item->stokobat->nama }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>{{ $item->expired ? \Carbon\Carbon::parse($item->expired)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Tidak ada data obat masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-3">
            {{ $data->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- Notifikasi -->
@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#2d7d32',
        timerProgressBar: true,
        timer: 3000,
    });
</script>
@endif

@if (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session("error") }}',
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#2d7d32',
    });
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById("unduh-excel").addEventListener("click", function (e) {
    e.preventDefault();
    unduhLaporan('excel');
});

document.getElementById("unduh-pdf").addEventListener("click", function (e) {
    e.preventDefault();
    unduhLaporan('pdf');
});

function unduhLaporan(format) {
    var startDate = document.getElementById("tgl_mulai").value;
    var endDate = document.getElementById("tgl_selesai").value;

    if (!startDate || !endDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops!',
            text: 'Pilih rentang tanggal terlebih dahulu!',
            confirmButtonColor: '#d33',
        });
        return;
    }

    // Tentukan URL berdasarkan format
    var url = (format === 'excel')
        ? "/laporanmasuk?tgl_mulai=" + startDate + "&tgl_selesai=" + endDate
        : "/laporanmasuk/pdf?tgl_mulai=" + startDate + "&tgl_selesai=" + endDate;

    window.location.href = url;
}
</script>
@endsection
