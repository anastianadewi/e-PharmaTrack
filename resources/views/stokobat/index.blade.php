@extends('layouts.app')

<!-- @section('title', 'Stok Obat') -->

@section('content')

<style>
.highlighted-card {
    background-color:rgb(28, 29, 82) !important; /* biru */
    box-shadow: 0 0 15px rgba(0, 123, 255, 0.3); /* bayangan biru */
    transition: all 0.5s ease-in-out;
    border-radius: 10px;
}
.card {
    position: relative;
}
.bg-light-danger {
    background-color: #f8d7da !important;
}

.bg-light-warning {
    background-color: #fff3cd !important;
}

</style>

<div class="container-fluid">

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-3 mb-4 ms-2">
        <h4 class="fw-bold" style="color: #0050A5;">Stok Obat</h4>
        @if(in_array(auth()->user()->role, ['nakes']))
        <button id="showFormBtn" class="btn btn-primary px-3">
            <i class="bi bi-plus-circle me-2"></i>Tambah Obat
        </button>
        @endif
    </div>

    {{-- Kategori --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        @foreach ($categories as $category)
            <a href="{{ route('stokobat.index', ['id_jenisobat' => $category->id_jenisobat]) }}">
                <button class="btn {{ request('id_jenisobat') == $category->id_jenisobat ? 'btn-warning' : 'btn-outline-warning' }} px-4 rounded-pill">
                    {{ $category->nama }}
                </button>
            </a>
        @endforeach
    </div>
    <hr>

    {{-- Search dan Tombol Unduh Laporan --}}
    <div class="row mb-3">

        {{-- Search --}}
        <div class="col-md-6 mb-2 mb-md-0">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="search" name="search" class="form-control border-start-0" placeholder="Quick search">
            </div>
        </div>

        {{-- Form Unduh Laporan --}}
        <div class="col-md-6 mb-2 mb-md-0">
            <div class="d-flex w-100" style="gap: 10px;">
                <div class="w-50">
                    <input type="date" name="tanggal" id="tanggalUnduh" class="form-control">
                </div>

                <div class="dropdown w-50">
                    <button class="btn btn-success dropdown-toggle w-100" type="button" id="dropdownUnduh" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-download"></i> Unduh Laporan
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownUnduh">
                        <li><a class="dropdown-item" href="#" id="unduh-excel">Excel</a></li>
                        <li><a class="dropdown-item" href="#" id="unduh-pdf">PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu Obat --}}
    <div class="row" id="stokobat-table">
        @foreach ($obatList as $obat)
            <div id="obat-{{ $obat->id_obat }}" class="col-md-3 mb-4">
                @php
                    $totalStok = $obat->detail->sum('jumlah');
                    $cardClass = 'card shadow-sm';

                    if ($totalStok == 0) {
                        $cardClass .= ' border-danger bg-light-danger';
                    } elseif ($totalStok <= 9) {
                        $cardClass .= ' border-warning bg-light-warning';
                    }
                @endphp
                <div class="{{ $cardClass }}">
                    @if ($obat->detail->isEmpty())
                        <form id="form-hapus-obat-{{ $obat->id_obat }}" action="{{ route('stokobat.destroyObat', $obat->id_obat) }}" method="POST" style="position: absolute; top: 6px; right: 8px;">
                            @csrf
                            @method('DELETE')
                            @if(in_array(auth()->user()->role, ['nakes']))
                            <button type="button" class="btn btn-sm p-0 m-0 text-danger" style="font-size: 20px;" title="Hapus Obat" onclick="konfirmasiHapusObat('{{ $obat->id_obat }}', '{{ $obat->nama }}')">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                            @endif
                        </form>
                    @endif
                    <div class="card-body">
                        <h6 class="fw-bold text-uppercase mb-1">{{ $obat->nama }}</h6>

                        {{-- total stok --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Stok :</span>
                            <h5 class="fw-bold mb-0" style="color:#6A1B9A">
                                {{ $obat->detail->sum('jumlah') }}
                            </h5>
                        </div>

                        {{-- golongan + keterangan --}}
                        <div class="mb-2 text-muted" style="font-size:0.85rem">
                            <div><i class="bi bi-bookmark-fill me-1 text-primary"></i><strong>Golongan:</strong>
                                {{ $obat->golongan ?? '-' }}
                            </div>
                            <div class="mt-1" title="{{ $obat->keterangan }}">
                                <i class="bi bi-info-circle-fill me-1 text-secondary"></i>
                                <strong>Ket.:</strong>
                                {{ Str::limit($obat->keterangan,40,'…') ?? '-' }}
                            </div>
                        </div>

                        <div class="mb-2">
                            <form id="form-hapus-detail-{{ $obat->id_obat }}" action="{{ route('stokobat.destroyDetail') }}" method="POST">            
                                @csrf
                                @method('DELETE')

                                @foreach ($obat->detail->sortBy('expired') as $detail)
                                    <div>
                                        @if(in_array(auth()->user()->role, ['nakes']))
                                        <input type="checkbox" name="ids_to_delete[]" value="{{ $detail->id_detailobat }}">
                                        @endif
                                        {{ \Carbon\Carbon::parse($detail->expired)->format('d/m/Y') }}
                                        <span class="ms-3">{{ $detail->jumlah }}</span>
                                    </div>
                                @endforeach
                            </form>
                        </div>

                        {{-- tombol aksi --}}
                        @if(in_array(auth()->user()->role, ['nakes']))
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-sm btn-primary"
                                    onclick="showTambahStokForm('{{ $obat->id_obat }}','{{ $obat->nama }}')">
                                + tambah stok obat
                            </button>

                            <button type="button" class="btn btn-sm btn-danger"
                                    onclick="submitHapus('{{ $obat->id_obat }}')"
                                    {{ $obat->detail->isEmpty()? 'disabled':'' }}>
                                <i class="bi bi-trash-fill"></i>
                            </button>

                            <button class="btn btn-sm btn-warning"
                                    onclick="showEditObatForm(
                                        '{{ $obat->id_obat }}',
                                        '{{ $obat->id_jenisobat }}',
                                        '{{ $obat->nama }}',
                                        '{{ $obat->golongan }}',
                                        `{{ $obat->keterangan ?? '' }}`)">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Popup Tambah Obat -->
<div id="popup-tambah-obat" style="display: none; position: fixed; top: 30px; left:0; width:100vw; height:100vh; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center;">
    <div style="background: white; padding: 20px; border-radius: 8px; width: 400px; position: relative;">
        <h5 class="mb-3">Tambah Obat</h5>
        <form id="form-tambah-obat" action="{{ route('stokobat.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="id_jenisobat" class="form-label">Jenis</label>
                <select name="id_jenisobat" class="form-select" required>
                    <option disabled selected>-- Pilih Jenis --</option>
                    @foreach ($jenisObatList as $jenis)
                        <option value="{{ $jenis->id_jenisobat }}">{{ $jenis->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Obat</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="golongan" class="form-label">Golongan Obat</label>
                <select name="golongan" class="form-control" required>
                    <option disabled selected>-- Pilih Golongan Obat --</option>
                    <option value="Obat Bebas">Obat Bebas</option>
                    <option value="Obat Bebas Terbatas">Obat Bebas Terbatas</option>
                    <option value="Obat Keras">Obat Keras</option>
                    <option value="Obat Jamu">Obat Jamu</option>
                    <option value="Obat Herbal Terstandar(OHT)">Obat Herbal Terstandar(OHT)</option>
                    <option value="Obat Fitofarmaka">Obat Fitofarmaka</option>
                    <option value="Obat Narkotika">Obat Narkotika</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" placeholder="Tulis Keterangan..."></textarea>
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

<!-- Popup Tambah Stok Obat -->
<div id="popup-tambah-stok" style="display: none; position: fixed; top:0; left:0; width:100vw; height:100vh; background: rgba(0,0,0,0.5); z-index: 1060; align-items: center; justify-content: center;">
    <div style="background: white; padding: 20px; border-radius: 8px; width: 400px; position: relative;">
        <h5 class="mb-3">Tambah Stok Obat: </h5>
        <div id="namaObatPopup" class="text-muted fs-6 mb-3"></div>
        <form id="form-tambah-stok" method="POST">
            @csrf
            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah Stok</label>
                <input type="number" name="jumlah" class="form-control" min="1" required>
            </div>
            <div class="mb-3">
                <label for="expired" class="form-label">Tanggal Expired</label>
                <input type="date" name="expired" class="form-control" required>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" id="btn-batal-stok" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
        <button id="close-x-stok" style="position: absolute; top: 8px; right: 8px; border: none; background: none; font-size: 20px; cursor: pointer;">&times;</button>
    </div>
</div>

<!-- Popup Edit Obat -->
<div id="popup-edit-obat" style="display: none; position: fixed; top: 30px; left:0; width:100vw; height:100vh; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center;">
    <div style="background: white; padding: 20px; border-radius: 8px; width: 400px; position: relative;">
        <h5 class="mb-3">Edit Obat</h5>
        <form id="form-edit-obat" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Jenis</label>
                <select name="id_jenisobat" id="edit_jenisobat" class="form-select" required>
                    @foreach ($jenisObatList as $jenis)
                        <option value="{{ $jenis->id_jenisobat }}">{{ $jenis->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Obat</label>
                <input type="text" name="nama" id="edit_nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Golongan Obat</label>
                <select name="golongan" id="edit_golongan" class="form-control" required>
                    <option value="Obat Bebas">Obat Bebas</option>
                    <option value="Obat Bebas Terbatas">Obat Bebas Terbatas</option>
                    <option value="Obat Keras">Obat Keras</option>
                    <option value="Obat Jamu">Obat Jamu</option>
                    <option value="Obat Herbal Terstandar(OHT)">Obat Herbal Terstandar(OHT)</option>
                    <option value="Obat Fitofarmaka">Obat Fitofarmaka</option>
                    <option value="Obat Narkotika">Obat Narkotika</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" id="edit_keterangan" class="form-control"></textarea>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" id="btn-batal-edit" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
        <button id="close-x-edit" style="position: absolute; top: 8px; right: 8px; border: none; background: none; font-size: 20px; cursor: pointer;">&times;</button>
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const popup = document.getElementById('popup-tambah-obat');

        document.getElementById('showFormBtn').addEventListener('click', () => {
            popup.style.display = 'flex';
        });

        document.getElementById('btn-batal').addEventListener('click', () => {
            popup.style.display = 'none';
            document.getElementById('form-tambah-obat').reset();
        });

        document.getElementById('close-x').addEventListener('click', () => {
            popup.style.display = 'none';
            document.getElementById('form-tambah-obat').reset();
        });

        popup.addEventListener('click', (e) => {
            if (e.target.id === 'popup-tambah-obat') {
                popup.style.display = 'none';
                document.getElementById('form-tambah-obat').reset();
            }
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Popup tambah obat sudah ada, sekarang popup tambah stok:

    const popupStok = document.getElementById('popup-tambah-stok');
    const formTambahStok = document.getElementById('form-tambah-stok');
    const namaObatPopup = document.getElementById('namaObatPopup');

    window.showTambahStokForm = (id_obat, nama) => {
        // Set nama obat di popup
        namaObatPopup.textContent = nama;

        // Set action form dinamis
        formTambahStok.action = `/stokobat/${id_obat}/tambah-stok`;

        // Tampilkan popup
        popupStok.style.display = 'flex';
    };

    // Tombol batal popup stok
    document.getElementById('btn-batal-stok').addEventListener('click', () => {
        popupStok.style.display = 'none';
        formTambahStok.reset();
    });

    // Tombol close popup stok
    document.getElementById('close-x-stok').addEventListener('click', () => {
        popupStok.style.display = 'none';
        formTambahStok.reset();
    });

    // Klik di luar popup stok
    popupStok.addEventListener('click', (e) => {
        if (e.target.id === 'popup-tambah-stok') {
            popupStok.style.display = 'none';
            formTambahStok.reset();
        }
    });
});
</script>

<script>
    function showEditObatForm(id, id_jenisobat, nama, golongan, keterangan) {
        const popup = document.getElementById('popup-edit-obat');
        document.getElementById('edit_jenisobat').value = id_jenisobat;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_golongan').value = golongan;
        document.getElementById('edit_keterangan').value = keterangan;

        const form = document.getElementById('form-edit-obat');
        form.action = `/stokobat/${id}`;

        popup.style.display = 'flex';
    }

    document.getElementById('btn-batal-edit').addEventListener('click', () => {
        document.getElementById('popup-edit-obat').style.display = 'none';
    });

    document.getElementById('close-x-edit').addEventListener('click', () => {
        document.getElementById('popup-edit-obat').style.display = 'none';
    });

    document.getElementById('popup-edit-obat').addEventListener('click', (e) => {
        if (e.target.id === 'popup-edit-obat') {
            document.getElementById('popup-edit-obat').style.display = 'none';
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function submitHapus(id) {
        const form = document.getElementById(`form-hapus-detail-${id}`);
        const checked = form.querySelectorAll('input[type="checkbox"]:checked');

        if (checked.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak ada yang dipilih',
                text: 'Silakan pilih stok yang ingin dihapus.'
            });
            return;
        }

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data stok yang dipilih akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#search').on('keyup', function () {
        let search = $(this).val();
        let kategori = "{{ request('id_jenisobat') }}";

        $.ajax({
            url: "{{ route('stokobat.index') }}",
            type: "GET",
            data: { search: search, id_jenisobat: kategori },
            success: function (response) {
                $('#stokobat-table').html($(response).find('#stokobat-table').html());
            }
        });
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const hash = window.location.hash;
    if (hash) {
        const target = document.querySelector(hash);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            target.classList.add('highlighted-card');

            setTimeout(() => {
                target.classList.remove('highlighted-card');
            }, 5000);
        }
    }
});
</script>

<script>
function konfirmasiHapusObat(id, nama) {
    Swal.fire({
        title: `Hapus obat "${nama}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`form-hapus-obat-${id}`).submit();
        }
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('unduh-excel').addEventListener('click', function(e) {
    e.preventDefault();
    let tanggal = document.getElementById('tanggalUnduh').value;
    if (!tanggal) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops!',
            text: 'Pilih tanggal terlebih dahulu!',
            confirmButtonColor: '#d33',
        });
        return;
    }
    window.location.href = `{{ route('stokobat.export.excel') }}?tanggal=${tanggal}&format=excel`;
});

document.getElementById('unduh-pdf').addEventListener('click', function(e) {
    e.preventDefault();
    let tanggal = document.getElementById('tanggalUnduh').value;
    if (!tanggal) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops!',
            text: 'Pilih tanggal terlebih dahulu!',
            confirmButtonColor: '#d33',
        });
        return;
    }
    window.location.href = `{{ route('stokobat.laporan.unduh') }}?tanggal=${tanggal}&format=pdf`;
});
</script>

@endsection
