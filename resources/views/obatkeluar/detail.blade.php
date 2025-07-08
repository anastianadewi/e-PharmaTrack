@extends('layouts.app')

<!-- @section('title', 'Detail Pengeluaran Obat') -->

@section('content')

<style>
.popup-layer {
    position: fixed;
    top: 30px;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    overflow-y: auto;
    padding: 20px;
}

.popup-box {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    width: 100%;
    max-width: 500px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 0 15px rgba(0,0,0,0.5);
}

.d-none {
    display: none !important;
}
</style>


<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #0050A5;">Detail Obat Keluar</h4>
        <a href="{{ route('obatkeluar.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Card Data Pasien -->
        <div class="col-md-6">
            <div class="card mb-4 h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>Data Pasien</span>
                    @if(in_array(auth()->user()->role, ['nakes']))
                    <button id="btnEditPasien" class="btn btn-sm btn-light text-primary">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    <p><strong>Nama:</strong> {{ $obatKeluar->nama }}</p>
                    <p><strong>Jenis Kelamin:</strong> {{ $obatKeluar->jenis_kelamin }}</p>
                    <p><strong>Keluhan:</strong> {{ $obatKeluar->keluhan }}</p>
                    <p><strong>Suhu Tubuh:</strong> {{ $obatKeluar->suhu_tubuh }} °C</p>
                    <p><strong>Denyut Nadi:</strong> {{ $obatKeluar->denyut_nadi }} bpm</p>
                    <p><strong>Tekanan Darah:</strong> {{ $obatKeluar->tekanan_darah }}</p>
                    <p><strong>Diagnosa:</strong> {{ $obatKeluar->diagnosa }}</p>
                    <p><strong>Keterangan:</strong> {{ $obatKeluar->keterangan }}</p>
                </div>
            </div>
        </div>

        <!-- Popup Form Edit Pasien -->
        <div id="popupEditPasien" class="popup-layer d-none">
            <div class="popup-box">
                <h5>Edit Data Pasien</h5>
                <form id="formEditPasien">
                    @csrf
                    <input type="hidden" name="id" value="{{ $obatKeluar->id_obatkeluar }}">
                    <div class="mb-2"><label>Nama</label><input class="form-control" name="nama" value="{{ $obatKeluar->nama }}"></div>
                    <div class="mb-2"><label>Jenis Kelamin</label>
                        <select class="form-control" name="jenis_kelamin">
                            <option value="Laki-laki" {{ $obatKeluar->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $obatKeluar->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-2"><label>Keluhan</label><input class="form-control" name="keluhan" value="{{ $obatKeluar->keluhan }}"></div>
                    <div class="mb-2"><label>Suhu Tubuh (°C)</label><input type="number" step="0.1" class="form-control" name="suhu_tubuh" value="{{ $obatKeluar->suhu_tubuh }}"></div>
                    <div class="mb-2"><label>Denyut Nadi (bpm)</label><input type="number" class="form-control" name="denyut_nadi" value="{{ $obatKeluar->denyut_nadi }}"></div>
                    <div class="mb-2"><label>Tekanan Darah (mmHg)</label><input class="form-control" name="tekanan_darah" value="{{ $obatKeluar->tekanan_darah }}"></div>
                    <div class="mb-2"><label>Diagnosa</label><input class="form-control" name="diagnosa" value="{{ $obatKeluar->diagnosa }}"></div>
                    <div class="mb-3"><label>Keterangan</label><input class="form-control" name="keterangan" value="{{ $obatKeluar->keterangan }}"></div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="togglePopup()">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card Obat -->
        <div class="col-md-6">
            <div class="card mb-4 h-100">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span>Obat yang Diberikan</span>
                    @if(in_array(auth()->user()->role, ['nakes']))
                    <a id="btnEditObat" class="btn btn-sm btn-light text-success">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach ($detailObat as $detail)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $detail->detailObat->stokobat->nama }}</strong>
                                    <br>
                                    <small class="text-muted">({{ $detail->detailObat->stokobat->jenisObat->nama }})</small>
                                    <br>
                                    <small class="text-muted">({{ $detail->detailObat->expired }})</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $detail->jumlah }}</span>
                            </li>
                        @endforeach
                        @if($detailObat->isEmpty())
                            <li class="list-group-item text-muted text-center">Tidak ada obat</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Popup Form Edit Obat -->
        <div id="popupEditObat" class="popup-layer d-none">
            <div class="popup-box">
                <h5>Edit Obat</h5>
                <form id="formEditObat">
                    @csrf
                    <input type="hidden" name="id_obatkeluar" value="{{ $obatKeluar->id_obatkeluar }}">

                    @foreach ($detailObat as $index => $detail)
                        <input type="hidden" name="id_detailobatkeluar[]" value="{{ $detail->id_detailobatkeluar }}">
                        <input type="hidden" name="id_obat[]" value="{{ $detail->detailObat->id_obat }}">
                        <div class="mb-2">
                            <label>{{ $detail->detailObat->stokobat->nama }}</label>
                            <small class="text-muted">({{ $detail->detailObat->stokobat->jenisObat->nama }})</small>
                            <input type="number" class="form-control" name="jumlah[]" value="{{ $detail->jumlah }}" min="1">
                        </div>
                    @endforeach
                    @csrf
                    <div id="obat-container"></div> {{-- Biarkan kosong, nanti JS isi --}}
                    <button type="button" id="add-obat" class="btn btn-sm btn-secondary mb-3">+ Tambah Obat</button>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="togglePopupObat()">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btnEditPasien').addEventListener('click', function () {
    togglePopup();
});

function togglePopup() {
    document.getElementById('popupEditPasien').classList.toggle('d-none');
}

document.getElementById('formEditPasien').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const id = formData.get('id');

    fetch(`/obatkeluar/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': form._token.value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Tampilkan alert berhasil
            Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data berhasil diperbarui.'
            });
            // Tutup popup
            document.getElementById('popupEditPasien').classList.add('d-none');
            // Optional: reload halaman agar data ter-update
            location.reload();
        } else {
            alert('Gagal menyimpan data');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan');
    });
});
</script>

<script>
document.getElementById('btnEditObat').addEventListener('click', function () {
    togglePopupObat();
});

function togglePopupObat() {
    document.getElementById('popupEditObat').classList.toggle('d-none');
}

function fetchObatOptions(selectElement) {
    fetch('/detailobatkeluar/obat-bersedia')
        .then(res => res.json())
        .then(obatList => {
            selectElement.innerHTML = '';
            obatList.forEach(obat => {
                const option = document.createElement('option');
                option.value = obat.id;
                option.textContent = `${obat.nama_obat} (${obat.nama_jenis})`;
                selectElement.appendChild(option);
            });
        });
}

function addObatRow() {
    const container = document.getElementById('obat-container');
    const newRow = document.createElement('div');
    newRow.classList.add('obat-row', 'mb-2', 'd-flex');

    const select = document.createElement('select');
    select.name = 'id_obat[]';
    select.classList.add('form-select', 'me-2');
    select.required = true;

    const input = document.createElement('input');
    input.type = 'number';
    input.name = 'jumlah[]';
    input.classList.add('form-control');
    input.placeholder = 'Jumlah';
    input.required = true;

    newRow.appendChild(select);
    newRow.appendChild(input);
    container.appendChild(newRow);

    fetchObatOptions(select);
}

// Tambah field obat baru saat tombol diklik
document.getElementById('add-obat').addEventListener('click', function () {
    fetch('/get-obat-bersedia')
        .then(res => res.json())
        .then(obatList => {
            const container = document.getElementById('obat-container');
            const index = container.children.length;

            const div = document.createElement('div');
            div.classList.add('mb-2');

            let html = `
                <label class="form-label">Obat Baru</label>
                <div class="d-flex gap-2">
                    <select name="id_obat[]" class="form-select" required style="flex: 2;">
                        <option value="">-- Pilih Obat --</option>`;
            obatList.forEach(obat => {
                html += `<option value="${obat.id}">${obat.nama_obat} (${obat.nama_jenis})</option>`;
            });
            html += `</select>
                    <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" min="1" required style="flex: 1;">
                    <input type="hidden" name="id_detailobatkeluar[]" value="">
            </div>`;

            div.innerHTML = html;
            container.appendChild(div);
        });
});

// Submit form edit obat
document.getElementById('formEditObat').addEventListener('submit', function (e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    fetch('/detailobatkeluar/update', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': form._token.value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data obat berhasil diperbarui.',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#2d7d32',
                timerProgressBar: true,
                timer: 3000,
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message || 'Terjadi kesalahan.'
            });
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan saat mengirim data.'
        });
    });
});
</script>

@endsection
