@extends('layouts.app')

<!-- @section('title', 'Pengeluaran Obat') -->

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mt-3 mb-4 ms-2">
        <h4 class="fw-bold" style="color: #0050A5;">Obat Keluar</h4>
    </div>

    <div class="row mt-3 mb-4">
        <form method="GET" action="{{ route('obatkeluar.index') }}">
            <div class="row g-3 align-items-end">
                <!-- Filter Tanggal -->
                <div class="col-md-2">
                    <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                    <input id="tgl_mulai" type="date" class="form-control" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
                </div>
                <div class="col-md-2">
                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                    <input id="tgl_selesai" type="date" class="form-control" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                </div>

                <!-- Tombol Aksi -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Tampilkan</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('obatkeluar.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
                <div class="col-md-2">
                    @if(in_array(auth()->user()->role, ['nakes']))
                    <button type="button" class="btn btn-primary w-100" id="btn-tambah">
                        <i class="bi bi-plus-circle"></i> Tambah Data
                    </button>
                    @endif
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
    </div>

    <div class="input-group mb-3">
        <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" id="search" name="search" class="form-control border-start-0" placeholder="Quick search">
    </div>

    <div class="table-responsive" id="obatkeluar-table">
        <table class="table table-bordered text-center align-middle">
            <thead style="border-bottom: 2px solid #333;">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Keluhan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($obatkeluar as $row)
                    <tr>
                        <td>{{ $obatkeluar->firstItem() + $loop->index }}</td>
                        <td>{{ $row->created_at->format('d/m/Y') }}</td>
                        <td>{{ $row->nama }}</td>
                        <td>{{ $row->jenis_kelamin }}</td>
                        <td>{{ $row->keluhan }}</td>
                        <td>
                            <a href="{{ route('obatkeluar.show', $row->id_obatkeluar) }}" class="btn btn-outline-info btn-sm me-1">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ route('obatkeluar.destroy', $row->id_obatkeluar) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                @if(in_array(auth()->user()->role, ['nakes']))
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">Tidak ada data obat keluar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-3">
            {{ $obatkeluar->withQueryString()->links() }}
        </div>
    </div>
</div>

<div id="popup-obatkeluar" style="display: none; position: fixed; top: 30px; left:0; width:100vw; height:100vh; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center;">
    <div style="background:white; padding:20px; border-radius:10px; width:600px; max-height:80vh; overflow:auto; position:relative;">
        <h5 id="judul-form">Tambah Obat Keluar</h5>

        {{-- FORM 1 - TABEL OBAT KELUAR --}}
        <form id="formObatKeluar">
            @csrf
            <label for="nama" class="form-label">Nama*</label>
            <input type="text" name="nama" class="form-control mb-2" placeholder="Nama Pasien" required>
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin*</label>
            <select name="jenis_kelamin" class="form-control mb-2" required>
                <option disabled selected>-- Pilih Jenis Kelamin --</option>
                <option value="Laki-Laki">Laki-Laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
            <label for="keluhan" class="form-label">Keluhan*</label>
            <input type="text" name="keluhan" class="form-control mb-2" placeholder="Keluhan" required>
            <label for="suhu_tubuh" class="form-label">Suhu Tubuh (°C)*</label>
            <input type="number" step="0.1" name="suhu_tubuh" class="form-control mb-2" placeholder="Suhu Tubuh (°C)" required>
            <label for="denyut_nadi" class="form-label">Denyut Nadi (bpm)*</label>
            <input type="number" name="denyut_nadi" class="form-control mb-2" placeholder="Denyut Nadi (bpm)" required>
            <label for="tekanan_darah" class="form-label">Tekanan Darah (mmHg)*</label>
            <input type="text" name="tekanan_darah" class="form-control mb-2" placeholder="Tekanan Darah (mmHg)" required>
            <label for="diagnosa" class="form-label">Diagnosa*</label>
            <input type="text" name="diagnosa" class="form-control mb-2" placeholder="Diagnosa" required>
            <label for="keterangan" class="form-label">Keterangan*</label>
            <input type="text" name="keterangan" class="form-control mb-3" placeholder="Keterangan">

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-secondary me-2" id="btn-cancel">Batal</button>
                <button type="button" class="btn btn-warning" id="next-step">Selanjutnya</button>
            </div>
        </form>

        {{-- FORM 2 - TABEL DETAIL OBAT KELUAR --}}
        <form id="formDetailObatKeluar" style="display: none;">
            @csrf
            <div id="obat-container">
                <div class="obat-row mb-2 d-flex align-items-center">
                    <select id="obat" name="id_obat[]" class="form-select me-2" required></select>
                    <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" min="1" required>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-obat ms-2">✕</button>
                </div>
            </div>
            <button type="button" id="add-obat" class="btn btn-sm btn-secondary mb-3">+ Tambah Obat</button>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-outline-secondary me-2" id="btn-back">Kembali</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
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

<script>
let popup = document.getElementById('popup-obatkeluar');
let form1 = document.getElementById('formObatKeluar');
let form2 = document.getElementById('formDetailObatKeluar');

document.getElementById('btn-tambah').addEventListener('click', () => {
    popup.style.display = 'flex';
    form1.style.display = 'block';
    form2.style.display = 'none';
    document.getElementById('judul-form').textContent = 'Tambah Obat Keluar';
});

document.getElementById('btn-cancel').addEventListener('click', () => {
    popup.style.display = 'none';
});

document.getElementById('btn-back').addEventListener('click', () => {
    form2.style.display = 'none';
    form1.style.display = 'block';
    document.getElementById('judul-form').textContent = 'Tambah Obat Keluar';
});

document.getElementById('next-step').addEventListener('click', async () => {
    const formData = new FormData(form1);

    try {
        const response = await fetch('/obatkeluar', {
            method: 'POST',
            body: formData
        });

        // Jika respons gagal (status bukan 2xx)
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Terjadi kesalahan saat menyimpan data.');
        }

        const data = await response.json();
        if (!data.id) {
            alert('Gagal menyimpan data pasien');
            return;
        }

        window.obatKeluarId = data.id;
        form1.style.display = 'none';
        form2.style.display = 'block';
        document.getElementById('judul-form').textContent = 'Tambah Detail Obat';
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message || 'Terjadi kesalahan tak terduga.',
            confirmButtonColor: '#d33',
        });
    }
});

document.getElementById('add-obat').addEventListener('click', () => {
    const container = document.getElementById('obat-container');
    const newRow = document.createElement('div');
    newRow.classList.add('obat-row', 'mb-2', 'd-flex');
    newRow.innerHTML = container.children[0].innerHTML;
    container.appendChild(newRow);
});

// Fungsi hapus baris jika tombol "X" diklik
    document.getElementById('obat-container').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-obat')) {
            e.target.closest('.obat-row').remove();
        }
    });

form2.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(form2);
    formData.append('id_obatkeluar', window.obatKeluarId);

    try {
        const response = await fetch('/detail-obatkeluar', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            const result = await response.json();
            throw new Error(result.error || result.message || 'Terjadi kesalahan saat menyimpan detail.');
        }

        const result = await response.json();
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data berhasil disimpan!',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#2d7d32',
                timerProgressBar: true,
                timer: 3000,
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error('Gagal menyimpan detail obat');
        }
    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: err.message,
            confirmButtonColor: '#d33',
        });
    }
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.btn-lihat-obat').forEach(button => {
            button.addEventListener('click', async () => {
                const id = button.dataset.id;
                const res = await fetch(`/obatkeluar/${id}`);
                const data = await res.json();

                let html = `<ul>`;
                data.forEach(item => {
                    html += `<li>${item.obat.nama} - Jumlah: ${item.jumlah}</li>`;
                });
                html += `</ul>`;

                alert('Obat Keluar:\n' + html); // Ganti dengan tampilkan di popup custom
            });
        });
    });
</script>

<script>
fetch('/get-obat-bersedia')
    .then(res => res.json())
    .then(data => {
        const selects = document.querySelectorAll('select[name="id_obat[]"]');
        selects.forEach(select => {
            select.innerHTML = '<option value="">Pilih Obat</option>'; // default
            data.forEach(obat => {
                const option = document.createElement('option');
                option.value = obat.id;
                option.textContent = `${obat.nama_obat} (${obat.nama_jenis})`;
                select.appendChild(option);
            });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const deleteForms = document.querySelectorAll('.delete-form');

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data ini akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});

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
                ? "/laporankeluar?tgl_mulai=" + startDate + "&tgl_selesai=" + endDate
                : "/laporankeluar/pdf?tgl_mulai=" + startDate + "&tgl_selesai=" + endDate;

            window.location.href = url;
}
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#search').on('keyup', function () {
        let search = $(this).val();

        $.ajax({
            url: "{{ route('obatkeluar.index') }}",
            type: "GET",
            data: { search: search },
            success: function (response) {
                $('#obatkeluar-table').html($(response).find('#obatkeluar-table').html());
            }
        });
    });
</script>
@endsection