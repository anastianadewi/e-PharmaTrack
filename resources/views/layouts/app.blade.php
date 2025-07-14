<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .sidebar {
            width: 60px;
            position: fixed;
            top: 63px; /* tingginya navbar */
            left: 0;
            height: calc(100vh - 63px);
            background-color: #13143a;
            z-index: 1050;
            transition: width 0.3s;
            overflow-x: hidden;
        }

        .sidebar a {
            text-decoration: none !important;
            color: white;
        }

        .sidebar.expanded {
            width: 200px;
        }

        .sidebar .sidebar-text {
            display: none;
            white-space: nowrap;
        }

        .sidebar-text {
            display: none;
        }

        .sidebar.expanded .sidebar-text {
            display: inline;
        }

        .sidebar-icon {
            color: white;
            font-size: 1.2rem;
            text-align: center;
            transition: background 0.3s;
            min-width: 40px;
        }

        .sidebar .nav-item {
            padding: 8px 10px;
            display: flex;
            align-items: center;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        .sidebar .nav-item:hover {
            background-color:rgba(255, 255, 255, 0.6);
        }

        .bg-navbar-light {
            background-color: rgb(220, 220, 220) !important;
            color: black !important;
        }

        .bg-navbar-light .sidebar-icon {
            color: black !important;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden; /* agar tidak scroll seluruh halaman */
        }

        .content {
            position: fixed;
            top: 43px; /* di bawah navbar */
            left: 60px;
            right: 0;
            bottom: 0;
            padding: 20px;
            overflow-y: auto; /* konten bisa discroll */
            transition: left 0.3s;
            background-color: #f8f9fa;
        }

        .content.shifted {
            left: 200px;
        }
        .text-purple {
            color: #6f42c1;
        }

        .btn-warning {
            background: linear-gradient(to bottom, #ffd700, #fbc531);
            border: 1px solid #f1c40f;
        }

        .btn-warning:hover {
            background: #f1c40f;
            color: black;
        }
    </style>
</head>
<body>
    @include('components.navbar')
    @include('components.sidebar')

    <div class="content" id="mainContent">
        <div class="sticky-top bg-white border-bottom d-flex justify-content-between align-items-center px-3 py-2" style="z-index: 1020;">
            <button id="toggleBtn" class="btn btn-sm btn-outline-dark me-3">☰</button>
            <!-- <h4 class="mb-0">@yield('title', 'Dashboard')</h4> -->

            <!-- Profile Dropdown -->
            <div class="dropdown">
                <a class="fw-semibold text-decoration-none text-dark dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">{{Auth::user()->nama}}</a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#verifyPasswordModal">Edit Profil</button></li>
                    <li>
                        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        @yield('content')
    </div>

    <!-- Modal 1: Verifikasi Password -->
    <div class="modal fade" id="verifyPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <form method="POST" action="{{ route('profile.verify') }}">
            @csrf
            <div class="modal-header">
            <h5 class="modal-title">Verifikasi Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($errors->has('current_password'))
                <div class="alert alert-danger mt-2 alert-dismissible fade show" id="alertBox">
                    {{ $errors->first('current_password') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <script>
                setTimeout(() => {
                    document.querySelectorAll('.alert').forEach(alertBox => {
                        const alert = bootstrap.Alert.getOrCreateInstance(alertBox);
                        alert.close();
                    });
                }, 3000);
            </script>
            <input type="password" name="current_password" class="form-control" placeholder="Masukkan password saat ini" required>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-warning text-dark">Lanjutkan</button>
            </div>
        </form>
        </div>
    </div>
    </div>

    <!-- Modal 2: Edit Profil -->
    <div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT') <!-- Ini yang membuat Laravel tahu bahwa ini request PUT -->
            <div class="modal-header">
                <h5 class="modal-title">Edit Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="nama" class="form-control mb-2" value="{{ old('nama', Auth::user()->nama) }}" placeholder="Nama">
                <input type="text" name="username" class="form-control mb-2" value="{{ old('username', Auth::user()->username) }}" placeholder="Username">
                <input type="password" name="password" class="form-control" placeholder="Password baru (jika diubah)">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
        </div>
    </div>
    </div>

    @if($errors->has('current_password'))
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const verifyModal = new bootstrap.Modal(document.getElementById('verifyPasswordModal'));
            verifyModal.show();

            // Auto-hide alert
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alertBox => {
                    const alert = bootstrap.Alert.getOrCreateInstance(alertBox);
                    alert.close();
                });
            }, 3000);
        });
    </script>
    @endif

    @if(session('edit_profile') && !$errors->has('current_password'))
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            let editModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
            editModal.show();
        });
    </script>
    @endif
    
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
        window.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('toggleBtn');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            // Restore sidebar state on load
            if (localStorage.getItem('sidebarExpanded') === 'true') {
                sidebar.classList.add('expanded');
                mainContent.classList.add('shifted');
            }

            toggleBtn?.addEventListener('click', () => {
                sidebar.classList.toggle('expanded');
                mainContent.classList.toggle('shifted');

                // Save to localStorage
                const isExpanded = sidebar.classList.contains('expanded');
                localStorage.setItem('sidebarExpanded', isExpanded);
            });
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const logoutForm = document.getElementById('logoutForm');
        if (logoutForm) {
            logoutForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Cegah submit langsung

                Swal.fire({
                    title: 'Konfirmasi Logout',
                    text: 'Apakah Anda yakin ingin keluar dari website ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, keluar',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        logoutForm.submit(); // Lanjutkan logout
                    }
                });
            });
        }
    });
    </script>

    <!-- <script>
        document.addEventListener('DOMContentLoaded', function () {
            const successAlert = document.getElementById('success-alert');
            if (successAlert) {
                setTimeout(() => {
                    const alert = bootstrap.Alert.getOrCreateInstance(successAlert);
                    alert.close();
                }, 3000);
            }
        });
    </script> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
