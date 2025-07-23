@extends('layouts.app')

<!-- @section('title', 'Jenis Obat') -->

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mt-3 mb-4 ms-2">
        <h4 class="fw-bold" style="color: #0050A5;">Jenis Obat</h4>
        <button id="showFormBtn" class="btn btn-primary px-3">
            <i class="bi bi-plus-circle me-2"></i> Tambah Jenis
        </button>
    </div>

    <div class="input-group mb-3">
        <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" id="search" name="search" class="form-control border-start-0" placeholder="Quick search">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead style="border-bottom: 2px solid #333;">
                <tr>
                    <th>No</th>
                    <th>Nama Jenis Obat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="jenisobat-table">
                @forelse ($jenisObat as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>
                        <form action="{{ route('jenisobat.destroy', $item->id_jenisobat) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3">Belum ada data jenis obat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Popup Tambah Jenis Obat -->
<div id="popup-tambah-jenis" style="display: none; position: fixed; top:0; left:0; width:100vw; height:100vh; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center;">
    <div style="background: white; padding: 20px; border-radius: 8px; width: 400px; position: relative;">
        <h5 class="mb-3">Tambah Jenis Obat</h5>
        <form id="form-tambah-jenis" action="{{ route('jenisobat.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Jenis Obat*</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" id="btn-batal" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
        <!-- Tombol close manual -->
        <button id="close-x" style="position: absolute; top: 8px; right: 8px; border: none; background: none; font-size: 20px; cursor: pointer;">&times;</button>
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
    document.addEventListener('DOMContentLoaded', () => {
        const popup = document.getElementById('popup-tambah-jenis');

        document.getElementById('showFormBtn').addEventListener('click', () => {
            popup.style.display = 'flex';
        });

        document.getElementById('btn-batal').addEventListener('click', () => {
            popup.style.display = 'none';
            document.getElementById('form-tambah-jenis').reset();
        });

        document.getElementById('close-x').addEventListener('click', () => {
            popup.style.display = 'none';
            document.getElementById('form-tambah-jenis').reset();
        });

        popup.addEventListener('click', (e) => {
            if (e.target.id === 'popup-tambah-jenis') {
                popup.style.display = 'none';
                document.getElementById('form-tambah-jenis').reset();
            }
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
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#search').on('keyup', function () {
        let search = $(this).val();

        $.ajax({
            url: "{{ route('jenisobat.index') }}",
            type: "GET",
            data: { search: search },
            success: function (response) {
                $('#jenisobat-table').html($(response).find('#jenisobat-table').html());
            }
        });
    });
</script>
@endsection
