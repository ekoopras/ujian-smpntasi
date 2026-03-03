<!doctype html>
<html>

<head>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>

    <h3>Data Nilai</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Nomor Absen</th>
                <th>Kelas</th>
                <th>Mapel</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $nilai)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $nilai->peserta->nama }}</td>
                    <td>{{ $nilai->peserta->nomor_absen }}</td>
                    <td>{{ $nilai->peserta->kelase->kelas }}</td>
                    <td>{{ $nilai->ujian->mapel->mapel ?? '-' }}</td>
                    <td>{{ $nilai->total_skor }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
