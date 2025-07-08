<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Obat</title>
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
    </style>
</head>
<body>

    <div class="kop">
        <h2>KLINIK RUTAN KELAS IIB TAMIANG LAYANG</h2>
        <p>Jl. Janah Munsit KM.1,5, Tamiang Layang, Kab. Barito Timur, Kalimantan Tengah</p>
        <h4>LAPORAN STOK OBAT</h4>
    </div>

    <div class="periode">
        <p>Tanggal: <strong>{{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</strong></p>
        <p>Diunduh oleh: {{ $user->nama }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Obat</th>
                <th>Jenis</th>
                <th>Stok per {{ $tanggal }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stokObats as $i => $obat)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $obat->nama }}</td>
                    <td>{{ $obat->jenisObat->nama }}</td>
                    <td>{{ $obat->stok_pada_tanggal }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
