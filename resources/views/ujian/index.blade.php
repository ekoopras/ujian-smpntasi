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

                <form method="POST" action="/ujian/start">
                    @csrf

                    <input class="form-control mb-3" name="nama" placeholder="Nama Lengkap" required>
                    <input class="form-control mb-3" name="nis" placeholder="NIS" required>

                    <select class="form-control mb-3" name="kelase_id" required>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->kelas }}</option>
                        @endforeach
                    </select>

                    <input class="form-control mb-3" name="token" placeholder="Token Ujian" required>

                    <button class="btn btn-primary w-100">Mulai Ujian</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
