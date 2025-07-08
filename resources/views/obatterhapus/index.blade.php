@extends('layouts.app')

<!-- @section('title', 'Obat Terhapus') -->

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mt-3 mb-4 ms-2">
        <h4 class="fw-bold" style="color: #0050A5;">Obat Terhapus</h4>
    </div>

    {{-- Search --}}
    <div class="input-group mb-3">
        <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" id="search" name="search" class="form-control border-start-0" placeholder="Quick search" value="{{ request('search') }}">
    </div>

    <div id="obatterhapus-wrapper" class="table-responsive">
        <table id="obatterhapus-table" class="table table-bordered align-middle text-center">
            <thead style="border-bottom: 2px solid #333;">
                <tr>
                    <th>No</th>
                    <th>Jenis</th>
                    <th>Golongan</th>
                    <th>Nama</th>
                    <th>Jumlah</th>
                    <th>Expired</th>
                    <th>Tanggal Dihapus</th>
                    <th>Dihapus Oleh</th>
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
                        <td>{{ $item->deleted_at ? \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') : '-' }}</td>
                        <td>{{ $item->deletedBy->nama ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Tidak ada data obat yang dihapus.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-3">
            {{ $data->withQueryString()->links() }}
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function fetchData(search = '', page = 1) {
        $.ajax({
            url: "{{ route('obatterhapus.index') }}",
            type: "GET",
            data: { search: search, page: page },
            success: function (response) {
                $('#obatterhapus-wrapper').html(
                    $(response).find('#obatterhapus-wrapper').html()
                );
            }
        });
    }

    $('#search').on('keyup', function () {
        const search = $(this).val();
        fetchData(search);
    });

    // Delegasi klik untuk tautan pagination (karena kontennya dinamis)
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        const search = $('#search').val();
        const url = $(this).attr('href');
        const page = new URL(url).searchParams.get('page');
        fetchData(search, page);
    });
</script>
@endsection
