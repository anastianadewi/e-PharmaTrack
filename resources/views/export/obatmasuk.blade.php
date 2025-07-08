<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Obat Masuk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .kop {
            text-align: center;
            margin-bottom: 20px;
        }

        .kop h2, .kop h4, .kop p {
            margin: 0;
            padding: 2px;
        }

        .periode {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .jenis-header {
            background-color: #ddd;
            font-weight: bold;
            text-align: left;
            padding-left: 10px;
        }
    </style>
</head>
<body>

    <div class="kop">
        <h2>KLINIK RUTAN KELAS IIB TAMIANG LAYANG</h2>
        <p>Jl. Janah Munsit KM.1,5, Tamiang Layang, Kab. Barito Timur, Kalimantan Tengah</p>
        <h4>LAPORAN OBAT MASUK</h4>
    </div>

    <div class="periode">
        <p>Periode: {{ \Carbon\Carbon::parse(request()->query('tgl_mulai'))->format('d M Y') }} 
        s/d 
        {{ \Carbon\Carbon::parse(request()->query('tgl_selesai'))->format('d M Y') }}</p>
        <p>Diunduh oleh: {{ $user->nama }}</p>
    </div>

    <table>
        <thead>
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
            @php $no = 1; @endphp
            @foreach ($obatmasuk as $jenis => $items)
                <tr>
                    <td colspan="7" class="jenis-header">{{ $jenis }}</td>
                </tr>
                @foreach ($items as $row)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $row->stokobat->jenisObat->nama ?? '-' }}</td>
                    <td>{{ $row->stokobat->golongan ?? '-' }}</td>
                    <td>{{ $row->stokobat->nama ?? '-' }}</td>
                    <td>{{ $row->jumlah }}</td>
                    <td>{{ $row->expired ? \Carbon\Carbon::parse($row->expired)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $row->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

</body>
</html>
