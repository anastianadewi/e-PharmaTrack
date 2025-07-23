@extends('layouts.app')

<!-- @section('title', 'User') -->

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mt-3 mb-4 ms-2">
        <h4 class="fw-bold" style="color: #0050A5;">User</h4>
        <button id="btn-tambah-user" class="btn btn-primary px-3">
            <i class="bi bi-person-plus-fill me-2"></i> Tambah User
        </button>
    </div>

    <!-- Search -->
    <div class="d-flex align-items-center mb-3 gap-2">
        <div class="flex-grow-1">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="search" name="search" class="form-control border-start-0" placeholder="Quick search">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead style="border-bottom: 2px solid #333;">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="user-table">
                @foreach ($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->nama }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <button
                            class="btn btn-outline-warning btn-sm me-1 btn-edit-user"
                            data-id="{{ $user->id_user }}"
                            data-nama="{{ $user->nama }}"
                            data-username="{{ $user->username }}"
                            data-role="{{ $user->role }}"
                        >
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <form action="{{ route('user.destroy', $user->id_user) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Popup Tambah User (default hidden) -->
<div id="popup-tambah-user" style="display: none; position: fixed; top: 20px; left:0; width:100vw; height:100vh; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center;">
    <div style="background: white; padding: 20px; border-radius: 8px; width: 400px; position: relative;">
        <h5>Tambah User</h5>
        <form id="form-tambah-user" action="{{ route('user.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nama" class="form-label">Nama*</label>
                <input type="text" name="nama" id="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username*</label>
                <input type="text" name="username" id="username" class="form-control" required pattern="\S+" oninput="this.value = this.value.replace(/\s/g, '')">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password (masukkan 8 karakter)*</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role*</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="nakes">Nakes</option>
                    <option value="pengelolaBMN">Pengelola BMN</option>
                    <option value="kepala">Kepala</option>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" id="btn-close-popup" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
        <!-- Tombol close manual -->
        <button id="close-x" style="position: absolute; top: 8px; right: 8px; border: none; background: none; font-size: 20px; cursor: pointer;">&times;</button>
    </div>
</div>

<!-- Popup Edit User -->
<div id="popup-edit-user" style="display: none; position: fixed; top: 20px; left:0; width:100vw; height:100vh; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center;">
    <div style="background: white; padding: 20px; border-radius: 8px; width: 400px; position: relative;">
        <h5>Edit User</h5>
        <form id="form-edit-user" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="edit-nama" class="form-label">Nama</label>
                <input type="text" name="nama" id="edit-nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="edit-username" class="form-label">Username</label>
                <input type="text" name="username" id="edit-username" class="form-control" required pattern="\S+" oninput="this.value = this.value.replace(/\s/g, '')">
            </div>
            <div class="mb-3">
                <label for="edit-password" class="form-label">Password (isi jika ingin diubah)</label>
                <input type="password" name="password" id="edit-password" class="form-control" placeholder="masukkan 8 karakter">
            </div>
            <div class="mb-3">
                <label for="edit-role" class="form-label">Role</label>
                <select name="role" id="edit-role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="nakes">Nakes</option>
                    <option value="pengelolaBMN">Pengelola BMN</option>
                    <option value="kepala">Kepala</option>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" id="btn-close-edit-popup" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
        <button id="close-edit-x" style="position: absolute; top: 8px; right: 8px; border: none; background: none; font-size: 20px; cursor: pointer;">&times;</button>
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
        timer: 4000,
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#search').on('keyup', function () {
        let search = $(this).val();

        $.ajax({
            url: "{{ route('user') }}",
            type: "GET",
            data: { search: search },
            success: function (response) {
                $('#user-table').html($(response).find('#user-table').html());
            }
        });
    });

    $(document).ready(function() {
        // Buka popup
        $('#btn-tambah-user').click(function() {
            $('#popup-tambah-user').css('display', 'flex');
        });

        // Tutup popup (klik tombol batal atau X)
        $('#btn-close-popup, #close-x').click(function() {
            $('#popup-tambah-user').hide();
            $('#form-tambah-user')[0].reset();
        });

        // Optional: tutup popup jika klik di luar form popup
        $('#popup-tambah-user').click(function(e) {
            if (e.target.id === 'popup-tambah-user') {
                $(this).hide();
                $('#form-tambah-user')[0].reset();
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
            e.preventDefault(); // cegah langsung submit

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data user ini akan dihapus secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // submit kalau konfirmasi "ya"
                }
            });
        });
    });
});
</script>

<script>
$(document).ready(function () {
    // Tampilkan popup edit dengan data user
    $('.btn-edit-user').click(function () {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        let username = $(this).data('username');
        let role = $(this).data('role');

        $('#edit-nama').val(nama);
        $('#edit-username').val(username);
        $('#edit-role').val(role);
        $('#edit-password').val('');

        // Atur action form sesuai ID
        $('#form-edit-user').attr('action', '/user/' + id);

        $('#popup-edit-user').css('display', 'flex');
    });

    // Tutup popup edit
    $('#btn-close-edit-popup, #close-edit-x').click(function () {
        $('#popup-edit-user').hide();
        $('#form-edit-user')[0].reset();
    });

    // Tutup popup jika klik di luar konten
    $('#popup-edit-user').click(function (e) {
        if (e.target.id === 'popup-edit-user') {
            $(this).hide();
            $('#form-edit-user')[0].reset();
        }
    });
});
</script>

@endsection
