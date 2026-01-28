<!doctype html>
<html lang="id">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body">
                <h4 class="mb-4">Masuk Ujian</h4>

                <form method="POST" action="/ujian/cek">
                    @csrf

                    <label>Nama</label>
                    <input type="text" name="nama" required>

                    <label>NIS</label>
                    <input type="text" name="nis" required>

                    <label>Kelas</label>
                    <select name="kelase_id" required>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->kelas }}</option>
                        @endforeach
                    </select>

                    <label>Kode Ujian</label>
                    <input type="text" name="kode_ujian" required>

                    <button type="submit">Lanjut</button>
                </form>


            </div>
        </div>
    </div>

</body>

</html>
