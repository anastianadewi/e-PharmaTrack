<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Obat Keluar</title>
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

        .summary-table {
            width: 50%;
        }

        .summary-table td {
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="kop">
        <h2>KLINIK RUTAN KELAS IIB TAMIANG LAYANG</h2>
        <p>Jl. Janah Munsit KM.1,5, Tamiang Layang, Kab. Barito Timur, Kalimantan Tengah</p>
        <h4>LAPORAN OBAT KELUAR</h4>
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
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Keluhan</th>
                <th>Suhu Tubuh(°C)</th>
                <th>Denyut Nadi(bpm)</th>
                <th>Tekanan Darah(mmHg)</th>
                <th>Diagnosa</th>
                <th>Keterangan</th>
                <th>Obat (Jenis)</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detailObatKeluar as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row->created_at->format('d/m/Y') }}</td>
                <td>{{ $row->obatKeluar->nama }}</td>
                <td>{{ $row->obatKeluar->jenis_kelamin }}</td>
                <td>{{ $row->obatKeluar->keluhan }}</td> 
                <td>{{ $row->obatKeluar->suhu_tubuh }}</td>
                <td>{{ $row->obatKeluar->denyut_nadi }}</td>
                <td>{{ $row->obatKeluar->tekanan_darah }}</td>
                <td>{{ $row->obatKeluar->diagnosa }}</td>
                <td>{{ $row->obatKeluar->keterangan }}</td>
                <td>{{ $row->detailObat->stokobat->nama }} ({{ $row->detailObat->stokobat->jenisObat->nama }})</td> 
                <td>{{ $row->jumlah }}</td> 
            </tr>
            @endforeach
        </tbody>
    </table>

    <h4 class="section-title">Rekapitulasi Total Penggunaan Obat</h4>

    <table class="summary-table">
        <thead>
            <tr>
                <th>Nama Obat (Jenis)</th>
                <th>Total Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($obatkeluar as $data)
            <tr>
                <td>{{ $data['nama_lengkap'] }}</td>
                <td>{{ $data['total'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
